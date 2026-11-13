<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\AppleWallet;

use Nowo\WalletQrBundle\Exception\WalletConfigurationException;

/**
 * SPI: builds the URL from which iOS downloads a `.pkpass` for a pass id.
 *
 * The bundle ships {@see AppleWalletPassLinkBuilder} (a URL pattern with a `{pass_id}` placeholder) and registers it as
 * `nowo_wallet_qr.apple_wallet.pass_link_builder` when `apple_wallet.enabled` is true. A host whose pass URLs cannot
 * be expressed as a pattern (signed or expiring URLs, per-tenant hosts) implements this interface and registers its
 * service under the same id.
 */
interface AppleWalletPassLinkBuilderInterface
{
    /**
     * Builds the pass download URL for a pass id.
     *
     * @param string $passId Pass identifier
     *
     * @throws WalletConfigurationException When the builder is not configured
     *
     * @return string Absolute URL of the pass
     */
    public function buildPassDownloadUrl(string $passId): string;
}
