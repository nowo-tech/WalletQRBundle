<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\Twig;

use Nowo\WalletQrBundle\Enum\WalletPlatform;
use Nowo\WalletQrBundle\Service\WalletQrService;
use Nowo\WalletQrBundle\Twig\WalletQrExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

final class WalletQrExtensionTest extends TestCase
{
    public function testWalletQrForUrl(): void
    {
        $extension = new WalletQrExtension(new WalletQrService(new \Nowo\WalletQrBundle\QrCode\QrCodeDataUriRenderer()));
        $result    = $extension->walletQrForUrl('ios', 'https://example.com/pass.pkpass');

        $this->assertSame(WalletPlatform::Ios, $result->link->platform);
    }

    public function testWalletQrDataUri(): void
    {
        $service   = new WalletQrService(new \Nowo\WalletQrBundle\QrCode\QrCodeDataUriRenderer());
        $extension = new WalletQrExtension($service);
        $walletQr  = $service->createQrForUrl(WalletPlatform::Android, 'https://example.com');

        $this->assertSame($walletQr->qrCodeDataUri, $extension->walletQrDataUri($walletQr));
    }

    public function testGetFunctions(): void
    {
        $extension = new WalletQrExtension(new WalletQrService(new \Nowo\WalletQrBundle\QrCode\QrCodeDataUriRenderer()));
        $functions = $extension->getFunctions();

        $this->assertCount(2, $functions);
        $this->assertContainsOnlyInstancesOf(TwigFunction::class, $functions);
        $this->assertSame(
            ['wallet_qr_data_uri', 'wallet_qr_for_url'],
            array_map(static fn (TwigFunction $function): string => $function->getName(), $functions),
        );
    }
}
