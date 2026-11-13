<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Service;

use Nowo\QrCodeBundle\Exception\InvalidQrUrlException;
use Nowo\WalletQrBundle\Enum\WalletPlatform;
use Nowo\WalletQrBundle\Exception\WalletConfigurationException;
use Nowo\WalletQrBundle\Model\GoogleWalletPassReference;
use Nowo\WalletQrBundle\Model\WalletQr;

/**
 * Port: creates Google / Apple Wallet QR codes.
 *
 * The bundle ships {@see WalletQrService} and aliases this interface to it. Type-hint the interface in application and
 * infrastructure code (test doubles, decoration); the Twig functions depend on it too.
 */
interface WalletQrServiceInterface
{
    /**
     * Creates the QR for a Google Wallet save link (Android).
     *
     * @param GoogleWalletPassReference $reference Pass class and object IDs
     * @param list<string>|null $origins JWT origins (null = configured default)
     *
     * @throws WalletConfigurationException When Google Wallet is disabled
     */
    public function createGoogleWalletQr(GoogleWalletPassReference $reference, ?array $origins = null): WalletQr;

    /**
     * Creates the QR for an Apple Wallet pass download URL (iOS).
     *
     * @param string $passId Pass identifier substituted into `{pass_id}`
     *
     * @throws WalletConfigurationException When Apple Wallet is disabled
     */
    public function createAppleWalletQr(string $passId): WalletQr;

    /**
     * Creates the Android and iOS pair for one pass.
     *
     * @param GoogleWalletPassReference $googleReference Google Wallet pass class and object IDs
     * @param string $applePassId Apple Wallet pass identifier
     * @param list<string>|null $googleOrigins JWT origins (null = configured default)
     *
     * @throws WalletConfigurationException When a platform is disabled
     *
     * @return array{android: WalletQr, ios: WalletQr}
     */
    public function createWalletQrPair(GoogleWalletPassReference $googleReference, string $applePassId, ?array $googleOrigins = null): array;

    /**
     * Creates a QR for an arbitrary http(s) URL (the QR profile's URL allowlist applies).
     *
     * @param WalletPlatform $platform Platform label attached to the link
     * @param string $url URL to encode
     *
     * @throws InvalidQrUrlException When the URL is not allowed
     */
    public function createQrForUrl(WalletPlatform $platform, string $url): WalletQr;
}
