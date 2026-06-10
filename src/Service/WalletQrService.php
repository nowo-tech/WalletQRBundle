<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Service;

use Nowo\WalletQrBundle\AppleWallet\AppleWalletPassLinkBuilder;
use Nowo\WalletQrBundle\Enum\WalletPlatform;
use Nowo\WalletQrBundle\Exception\WalletConfigurationException;
use Nowo\WalletQrBundle\GoogleWallet\GoogleWalletSaveLinkBuilder;
use Nowo\WalletQrBundle\Model\GoogleWalletPassReference;
use Nowo\WalletQrBundle\Model\WalletLink;
use Nowo\WalletQrBundle\Model\WalletQr;
use Nowo\WalletQrBundle\QrCode\QrCodeDataUriRenderer;

/**
 * High-level API to create wallet save links and QR codes for Android and iOS.
 */
final class WalletQrService
{
    public function __construct(
        private readonly QrCodeDataUriRenderer $qrCodeRenderer,
        private readonly ?GoogleWalletSaveLinkBuilder $googleWalletSaveLinkBuilder = null,
        private readonly ?AppleWalletPassLinkBuilder $appleWalletPassLinkBuilder = null,
    ) {
    }

    /**
     * @param list<string>|null $origins
     */
    public function createGoogleWalletQr(
        GoogleWalletPassReference $reference,
        ?array $origins = null,
    ): WalletQr {
        $link = new WalletLink(
            WalletPlatform::Android,
            $this->requireGoogleBuilder()->buildSaveLink($reference, $origins),
        );

        return new WalletQr($link, $this->qrCodeRenderer->renderDataUri($link->url));
    }

    public function createAppleWalletQr(string $passId): WalletQr
    {
        $link = new WalletLink(
            WalletPlatform::Ios,
            $this->requireAppleBuilder()->buildPassDownloadUrl($passId),
        );

        return new WalletQr($link, $this->qrCodeRenderer->renderDataUri($link->url));
    }

    /**
     * @param list<string>|null $googleOrigins
     *
     * @return array{android: WalletQr, ios: WalletQr}
     */
    public function createWalletQrPair(
        GoogleWalletPassReference $googleReference,
        string $applePassId,
        ?array $googleOrigins = null,
    ): array {
        return [
            'android' => $this->createGoogleWalletQr($googleReference, $googleOrigins),
            'ios'     => $this->createAppleWalletQr($applePassId),
        ];
    }

    public function createQrForUrl(WalletPlatform $platform, string $url): WalletQr
    {
        $link = new WalletLink($platform, $url);

        return new WalletQr($link, $this->qrCodeRenderer->renderDataUri($url));
    }

    private function requireGoogleBuilder(): GoogleWalletSaveLinkBuilder
    {
        if (!$this->googleWalletSaveLinkBuilder instanceof GoogleWalletSaveLinkBuilder) {
            throw new WalletConfigurationException('Google Wallet integration is disabled or not configured.');
        }

        return $this->googleWalletSaveLinkBuilder;
    }

    private function requireAppleBuilder(): AppleWalletPassLinkBuilder
    {
        if (!$this->appleWalletPassLinkBuilder instanceof AppleWalletPassLinkBuilder) {
            throw new WalletConfigurationException('Apple Wallet integration is disabled or not configured.');
        }

        return $this->appleWalletPassLinkBuilder;
    }
}
