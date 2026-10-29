<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Service;

use Nowo\QrCodeBundle\Service\QrCodeService;
use Nowo\WalletQrBundle\AppleWallet\AppleWalletPassLinkBuilder;
use Nowo\WalletQrBundle\Enum\WalletPlatform;
use Nowo\WalletQrBundle\Exception\WalletConfigurationException;
use Nowo\WalletQrBundle\GoogleWallet\GoogleWalletSaveLinkBuilder;
use Nowo\WalletQrBundle\Model\GoogleWalletPassReference;
use Nowo\WalletQrBundle\Model\WalletLink;
use Nowo\WalletQrBundle\Model\WalletQr;

/**
 * High-level API to create wallet save links and QR codes for Android and iOS.
 *
 * QR rendering and URL policy are provided by nowo-tech/qr-code-bundle ({@see QrCodeService}).
 */
final readonly class WalletQrService
{
    /**
     * Creates the wallet QR service.
     *
     * @param QrCodeService $qrCodeService QR renderer from nowo-tech/qr-code-bundle
     * @param GoogleWalletSaveLinkBuilder|null $googleWalletSaveLinkBuilder Null when Google Wallet is disabled
     * @param AppleWalletPassLinkBuilder|null $appleWalletPassLinkBuilder Null when Apple Wallet is disabled
     */
    public function __construct(
        private QrCodeService $qrCodeService,
        private ?GoogleWalletSaveLinkBuilder $googleWalletSaveLinkBuilder = null,
        private ?AppleWalletPassLinkBuilder $appleWalletPassLinkBuilder = null,
    ) {
    }

    /**
     * Creates the Android (Google Wallet) save link and its QR code.
     *
     * @param GoogleWalletPassReference $reference Pass class and object IDs
     * @param list<string>|null $origins JWT origins (null = configured default)
     *
     * @throws WalletConfigurationException when Google Wallet is disabled
     */
    public function createGoogleWalletQr(
        GoogleWalletPassReference $reference,
        ?array $origins = null,
    ): WalletQr {
        $link = new WalletLink(
            WalletPlatform::Android,
            $this->requireGoogleBuilder()->buildSaveLink($reference, $origins),
        );

        return new WalletQr($link, $this->qrCodeService->createDataUri($link->url));
    }

    /**
     * Creates the iOS (Apple Wallet) pass download link and its QR code.
     *
     * @param string $passId Pass identifier substituted into `{pass_id}`
     *
     * @throws WalletConfigurationException when Apple Wallet is disabled
     */
    public function createAppleWalletQr(string $passId): WalletQr
    {
        $link = new WalletLink(
            WalletPlatform::Ios,
            $this->requireAppleBuilder()->buildPassDownloadUrl($passId),
        );

        return new WalletQr($link, $this->qrCodeService->createDataUri($link->url));
    }

    /**
     * Creates both the Android and the iOS wallet QR codes.
     *
     * @param GoogleWalletPassReference $googleReference Google Wallet pass class and object IDs
     * @param string $applePassId Apple Wallet pass identifier
     * @param list<string>|null $googleOrigins JWT origins (null = configured default)
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

    /**
     * Creates a QR code for an arbitrary http(s) URL (checked by the nowo_qr_code URL policy).
     *
     * @param WalletPlatform $platform Platform label attached to the link
     * @param string $url URL to encode
     */
    public function createQrForUrl(WalletPlatform $platform, string $url): WalletQr
    {
        $link = new WalletLink($platform, $url);

        return new WalletQr($link, $this->qrCodeService->createDataUriForUrl($url));
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
