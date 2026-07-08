<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\Service;

use Nowo\WalletQrBundle\AppleWallet\AppleWalletPassLinkBuilder;
use Nowo\WalletQrBundle\Enum\WalletPlatform;
use Nowo\WalletQrBundle\Exception\InvalidWalletQrUrlException;
use Nowo\WalletQrBundle\Exception\WalletConfigurationException;
use Nowo\WalletQrBundle\GoogleWallet\GoogleWalletSaveLinkBuilder;
use Nowo\WalletQrBundle\Model\GoogleWalletPassReference;
use Nowo\WalletQrBundle\QrCode\QrCodeDataUriRenderer;
use Nowo\WalletQrBundle\Security\QrUrlPolicy;
use Nowo\WalletQrBundle\Service\WalletQrService;
use PHPUnit\Framework\TestCase;

use function dirname;

final class WalletQrServiceTest extends TestCase
{
    private string $fixturePath;

    protected function setUp(): void
    {
        $this->fixturePath = dirname(__DIR__, 2) . '/fixtures/google-service-account.json';
    }

    public function testCreateQrForUrl(): void
    {
        $service = new WalletQrService(new QrCodeDataUriRenderer(), new QrUrlPolicy());
        $result  = $service->createQrForUrl(WalletPlatform::Android, 'https://example.com/save');

        $this->assertSame(WalletPlatform::Android, $result->link->platform);
        $this->assertSame('https://example.com/save', $result->link->url);
        $this->assertStringStartsWith('data:image/png;base64,', $result->qrCodeDataUri);
    }

    public function testCreateQrForUrlRejectsUnsafeScheme(): void
    {
        $this->expectException(InvalidWalletQrUrlException::class);

        $service = new WalletQrService(new QrCodeDataUriRenderer(), new QrUrlPolicy());
        $service->createQrForUrl(WalletPlatform::Android, 'javascript:alert(1)');
    }

    public function testCreateGoogleWalletQr(): void
    {
        $service = new WalletQrService(
            new QrCodeDataUriRenderer(),
            new QrUrlPolicy(),
            new GoogleWalletSaveLinkBuilder('3388000000000000000', $this->fixturePath),
        );

        $reference = GoogleWalletPassReference::withIssuer('3388000000000000000', 'OBJ', 'CLS');
        $result    = $service->createGoogleWalletQr($reference);

        $this->assertSame(WalletPlatform::Android, $result->link->platform);
        $this->assertStringStartsWith('https://pay.google.com/gp/v/save/', $result->link->url);
    }

    public function testCreateAppleWalletQr(): void
    {
        $service = new WalletQrService(
            new QrCodeDataUriRenderer(),
            new QrUrlPolicy(),
            null,
            new AppleWalletPassLinkBuilder('https://example.com/{pass_id}.pkpass'),
        );

        $result = $service->createAppleWalletQr('ticket-42');

        $this->assertSame(WalletPlatform::Ios, $result->link->platform);
        $this->assertSame('https://example.com/ticket-42.pkpass', $result->link->url);
    }

    public function testCreateWalletQrPair(): void
    {
        $service = new WalletQrService(
            new QrCodeDataUriRenderer(),
            new QrUrlPolicy(),
            new GoogleWalletSaveLinkBuilder('3388000000000000000', $this->fixturePath),
            new AppleWalletPassLinkBuilder('https://example.com/{pass_id}.pkpass'),
        );

        $pair = $service->createWalletQrPair(
            GoogleWalletPassReference::withIssuer('3388000000000000000', 'OBJ', 'CLS'),
            'ticket-42',
        );

        $this->assertSame(WalletPlatform::Android, $pair['android']->link->platform);
        $this->assertSame(WalletPlatform::Ios, $pair['ios']->link->platform);
    }

    public function testGoogleWalletDisabledThrows(): void
    {
        $this->expectException(WalletConfigurationException::class);

        $service = new WalletQrService(new QrCodeDataUriRenderer(), new QrUrlPolicy());
        $service->createGoogleWalletQr(
            GoogleWalletPassReference::withIssuer('3388000000000000000', 'OBJ', 'CLS'),
        );
    }

    public function testAppleWalletDisabledThrows(): void
    {
        $this->expectException(WalletConfigurationException::class);

        $service = new WalletQrService(new QrCodeDataUriRenderer(), new QrUrlPolicy());
        $service->createAppleWalletQr('ticket-42');
    }
}
