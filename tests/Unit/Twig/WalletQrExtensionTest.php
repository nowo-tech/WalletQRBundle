<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\Twig;

use Nowo\WalletQrBundle\Enum\WalletPlatform;
use Nowo\WalletQrBundle\Service\WalletQrService;
use Nowo\WalletQrBundle\Twig\WalletQrExtension;
use PHPUnit\Framework\TestCase;

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
}
