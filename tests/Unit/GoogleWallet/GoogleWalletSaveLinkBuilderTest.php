<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\GoogleWallet;

use Nowo\WalletQrBundle\Exception\WalletConfigurationException;
use Nowo\WalletQrBundle\GoogleWallet\GoogleWalletSaveLinkBuilder;
use Nowo\WalletQrBundle\Model\GoogleWalletPassReference;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;
use Throwable;

use function array_column;
use function base64_decode;
use function dirname;
use function explode;
use function file_put_contents;
use function is_array;
use function json_decode;
use function json_encode;
use function str_contains;
use function strlen;
use function strtr;
use function substr;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

final class GoogleWalletSaveLinkBuilderTest extends TestCase
{
    private string $fixturePath;

    protected function setUp(): void
    {
        $this->fixturePath = dirname(__DIR__, 2) . '/fixtures/google-service-account.json';
    }

    public function testBuildSaveLink(): void
    {
        $builder = new GoogleWalletSaveLinkBuilder(
            '3388000000000000000',
            $this->fixturePath,
            ['example.com'],
        );

        $reference = GoogleWalletPassReference::withIssuer(
            '3388000000000000000',
            'OBJECT_SUFFIX',
            'CLASS_SUFFIX',
        );

        $url = $builder->buildSaveLink($reference);

        $this->assertStringStartsWith('https://pay.google.com/gp/v/save/', $url);
        $this->assertGreaterThan(100, strlen($url));
    }

    public function testBuildSaveLinkFromPayload(): void
    {
        $builder = new GoogleWalletSaveLinkBuilder('3388000000000000000', $this->fixturePath);
        $url     = $builder->buildSaveLinkFromPayload([
            'genericObjects' => [[
                'id'      => '3388000000000000000.OBJECT',
                'classId' => '3388000000000000000.CLASS',
            ]],
        ]);

        $this->assertStringStartsWith('https://pay.google.com/gp/v/save/', $url);
    }

    public function testMissingServiceAccountFileThrows(): void
    {
        $this->expectException(WalletConfigurationException::class);

        $builder = new GoogleWalletSaveLinkBuilder('3388000000000000000', '/missing/file.json');
        $builder->buildSaveLinkFromPayload(['genericObjects' => []]);
    }

    public function testIsConfigured(): void
    {
        $this->assertTrue(GoogleWalletSaveLinkBuilder::isConfigured('3388000000000000000', $this->fixturePath));
        $this->assertFalse(GoogleWalletSaveLinkBuilder::isConfigured('', $this->fixturePath));
    }

    public function testIatClaimComesFromInjectedClock(): void
    {
        $clock   = new MockClock('2026-01-02 03:04:05 UTC');
        $builder = new GoogleWalletSaveLinkBuilder('3388000000000000000', $this->fixturePath, [], $clock);

        $claims = $this->decodeClaims($builder->buildSaveLinkFromPayload(['genericObjects' => []]));

        $this->assertSame($clock->now()->getTimestamp(), $claims['iat']);
        $this->assertArrayNotHasKey('origins', $claims);
    }

    public function testSuccessIsLoggedWithoutSecrets(): void
    {
        $logger  = new InMemoryLogger();
        $builder = new GoogleWalletSaveLinkBuilder('3388000000000000000', $this->fixturePath, ['example.com'], null, $logger);

        $url = $builder->buildSaveLinkFromPayload(['genericObjects' => [['id' => 'OBJECT-ID']]]);

        $this->assertSame(['debug', 'info'], array_column($logger->records, 'level'));
        $this->assertSame('google_wallet.sign_save_link', $logger->records[1]['context']['action']);
        $this->assertSame(1, $logger->records[1]['context']['origins']);
        $this->assertNoSecretsLogged($logger, substr($url, strlen('https://pay.google.com/gp/v/save/')));
    }

    public function testConfigurationFailureIsLoggedAsError(): void
    {
        $logger  = new InMemoryLogger();
        $builder = new GoogleWalletSaveLinkBuilder('3388000000000000000', '/missing/file.json', [], null, $logger);

        try {
            $builder->buildSaveLinkFromPayload(['genericObjects' => []]);
            $this->fail('Expected WalletConfigurationException.');
        } catch (WalletConfigurationException) {
        }

        $this->assertSame(['debug', 'error'], array_column($logger->records, 'level'));
        $this->assertStringContainsString('not found', (string) $logger->records[1]['context']['reason']);
    }

    public function testSigningFailureIsLoggedWithoutExceptionMessage(): void
    {
        $path = (string) tempnam(sys_get_temp_dir(), 'wallet-qr-sa');
        // Obvious placeholder, not a key: makes the RS256 signer fail.
        file_put_contents($path, (string) json_encode([
            'client_email' => 'placeholder@example.invalid',
            'private_key'  => 'REDACTED-TEST-NOT-A-KEY',
        ]));

        $logger  = new InMemoryLogger();
        $builder = new GoogleWalletSaveLinkBuilder('3388000000000000000', $path, [], null, $logger);

        $thrown = null;
        try {
            $builder->buildSaveLinkFromPayload(['genericObjects' => []]);
        } catch (Throwable $e) {
            $thrown = $e;
        } finally {
            unlink($path);
        }

        $this->assertInstanceOf(Throwable::class, $thrown, 'Expected a signing failure.');
        $this->assertNotInstanceOf(WalletConfigurationException::class, $thrown);
        $this->assertSame(['debug', 'error'], array_column($logger->records, 'level'));
        $this->assertArrayHasKey('exception_class', $logger->records[1]['context']);
        $this->assertArrayNotHasKey('reason', $logger->records[1]['context']);
        $this->assertNoSecretsLogged($logger, 'REDACTED-TEST-NOT-A-KEY');
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeClaims(string $url): array
    {
        $jwt     = substr($url, strlen('https://pay.google.com/gp/v/save/'));
        $segment = explode('.', $jwt)[1];
        $claims  = json_decode((string) base64_decode(strtr($segment, '-_', '+/'), true), true);
        $this->assertTrue(is_array($claims));

        /* @var array<string, mixed> $claims */
        return $claims;
    }

    private function assertNoSecretsLogged(InMemoryLogger $logger, string $secret): void
    {
        $dump = (string) json_encode($logger->records);
        $this->assertFalse(str_contains($dump, $secret), 'A secret value was logged.');
        $this->assertFalse(str_contains($dump, 'PRIVATE KEY'), 'Private key material was logged.');
        $this->assertFalse(str_contains($dump, 'OBJECT-ID'), 'The pass payload was logged.');
    }
}
