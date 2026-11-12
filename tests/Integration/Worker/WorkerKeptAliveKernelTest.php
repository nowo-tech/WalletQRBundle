<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Integration\Worker;

use Nowo\WalletQrBundle\AppleWallet\AppleWalletPassLinkBuilder;
use Nowo\WalletQrBundle\GoogleWallet\GoogleWalletSaveLinkBuilder;
use Nowo\WalletQrBundle\Model\GoogleWalletPassReference;
use Nowo\WalletQrBundle\Service\WalletQrService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

use function base64_decode;
use function explode;
use function gc_collect_cycles;
use function json_decode;
use function json_encode;
use function memory_get_usage;
use function sprintf;
use function str_starts_with;
use function strtr;
use function substr;
use function sys_get_temp_dir;

use const JSON_THROW_ON_ERROR;

/**
 * REQ-RUNTIME-002: one kept-alive kernel, the services resetter never runs (FrankenPHP worker mode with kernel reset
 * false), N Google / Apple Wallet QR creations as two users in alternation through the public service.
 *
 * Each link and QR must carry only its own user's pass data, nothing may carry over between users, and memory must
 * stay flat.
 */
final class WorkerKeptAliveKernelTest extends TestCase
{
    private const ITERATIONS = 1000;

    private const QR_EVERY = 25;

    private const WARM_UP = 50;

    private const MEMORY_MARGIN_BYTES = 4 * 1024 * 1024;

    protected function tearDown(): void
    {
        (new Filesystem())->remove(sys_get_temp_dir() . '/wallet_qr_kept_alive');
    }

    public function testQrCreationInOneKernelWithoutResetterKeepsUsersSeparatedAndMemoryFlat(): void
    {
        $kernel = new KeptAliveKernel('test', false);
        $kernel->boot();
        // Fetched once and kept, as a kept-alive worker does; `services_resetter` is never called.
        $service = $kernel->getContainer()->get('test.wallet_qr');
        self::assertInstanceOf(WalletQrService::class, $service);

        $builder = $kernel->getContainer()->get('test.google_builder');
        self::assertInstanceOf(GoogleWalletSaveLinkBuilder::class, $builder);

        $appleBuilder = $kernel->getContainer()->get('test.apple_builder');
        self::assertInstanceOf(AppleWalletPassLinkBuilder::class, $appleBuilder);

        $appleQr  = [];
        $baseline = 0;

        for ($i = 0; $i < self::ITERATIONS; ++$i) {
            if ($i === self::WARM_UP) {
                gc_collect_cycles();
                $baseline = memory_get_usage();
            }

            $user  = $i % 2 === 0 ? 'alpha' : 'beta';
            $other = $user === 'alpha' ? 'beta' : 'alpha';
            $at    = sprintf('iteration %d (%s)', $i, $user);

            $reference = GoogleWalletPassReference::withIssuer('3388000000000000000', 'obj-' . $user, 'cls-' . $user);
            $origins   = $user === 'alpha' ? null : ['beta.example.com'];

            // Every iteration signs the link; the PNG of the (long) JWT is rendered only every QR_EVERY iterations.
            if ($i % self::QR_EVERY === 0) {
                $google = $service->createGoogleWalletQr($reference, $origins);
                self::assertStringStartsWith('data:image/png;base64,', $google->qrCodeDataUri, $at);
                $url = $google->link->url;
            } else {
                $url = $builder->buildSaveLink($reference, $origins);
            }
            self::assertTrue(str_starts_with($url, 'https://pay.google.com/gp/v/save/'), $at);

            $claims = $this->decodeJwtClaims(substr($url, 33));
            self::assertSame('3388000000000000000.obj-' . $user, $claims['payload']['genericObjects'][0]['id'] ?? null, $at);
            self::assertSame($user === 'alpha' ? ['example.com'] : ['beta.example.com'], $claims['origins'], $at);
            self::assertStringNotContainsString('obj-' . $other, json_encode($claims, JSON_THROW_ON_ERROR), $at);

            $applePassId = 'pass-' . $user;
            if ($i % self::QR_EVERY === 0) {
                $apple    = $service->createAppleWalletQr($applePassId);
                $appleUrl = $apple->link->url;
                // The QR of a given pass is stable across the whole run and never equals the other user's.
                $appleQr[$user] ??= $apple->qrCodeDataUri;
                self::assertSame($appleQr[$user], $apple->qrCodeDataUri, $at);
                if (isset($appleQr[$other])) {
                    self::assertNotSame($appleQr[$other], $apple->qrCodeDataUri, $at);
                }
            } else {
                $appleUrl = $appleBuilder->buildPassDownloadUrl($applePassId);
            }
            self::assertSame('https://passes.example.com/pass-' . $user . '.pkpass', $appleUrl, $at);
        }

        gc_collect_cycles();
        $growth = memory_get_usage() - $baseline;
        self::assertLessThan(self::MEMORY_MARGIN_BYTES, $growth, sprintf('memory grew by %d bytes over %d iterations', $growth, self::ITERATIONS - self::WARM_UP));

        $kernel->shutdown();
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJwtClaims(string $jwt): array
    {
        $parts = explode('.', $jwt);
        self::assertCount(3, $parts);

        return json_decode((string) base64_decode(strtr($parts[1], '-_', '+/'), true), true, 512, JSON_THROW_ON_ERROR);
    }
}
