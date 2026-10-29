<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\AppleWallet;

use Nowo\WalletQrBundle\Exception\WalletConfigurationException;

use function str_contains;
use function str_replace;
use function trim;

/**
 * Builds Apple Wallet pass download URLs for QR encoding.
 *
 * Apple Wallet passes are typically served as `.pkpass` files. This builder resolves
 * a configured URL pattern so applications can expose a scannable link for iOS devices.
 */
final readonly class AppleWalletPassLinkBuilder
{
    /**
     * Creates the builder for a configured URL pattern.
     *
     * @param string $passDownloadUrlPattern URL pattern containing the `{pass_id}` placeholder
     */
    public function __construct(
        private string $passDownloadUrlPattern,
    ) {
    }

    /**
     * Resolves the `.pkpass` download URL for a pass ID (URL-encoded into `{pass_id}`).
     *
     * @param string $passId Pass identifier
     *
     * @throws WalletConfigurationException when the pattern is empty or has no placeholder
     */
    public function buildPassDownloadUrl(string $passId): string
    {
        if (trim($this->passDownloadUrlPattern) === '') {
            throw new WalletConfigurationException('Apple Wallet pass_download_url_pattern is not configured.');
        }

        if (!str_contains($this->passDownloadUrlPattern, '{pass_id}')) {
            throw new WalletConfigurationException('Apple Wallet pass_download_url_pattern must contain the "{pass_id}" placeholder.');
        }

        return str_replace('{pass_id}', rawurlencode($passId), $this->passDownloadUrlPattern);
    }

    /**
     * Tells whether the pattern is non-empty and contains `{pass_id}`.
     *
     * @param string $passDownloadUrlPattern URL pattern to check
     */
    public static function isConfigured(string $passDownloadUrlPattern): bool
    {
        return trim($passDownloadUrlPattern) !== '' && str_contains($passDownloadUrlPattern, '{pass_id}');
    }
}
