<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\GoogleWallet;

use Nowo\WalletQrBundle\Exception\WalletConfigurationException;
use Nowo\WalletQrBundle\Model\GoogleWalletPassReference;
use Nowo\WalletQrBundle\Service\WalletQrService;

/**
 * SPI: builds the signed "Add to Google Wallet" save link for a pass.
 *
 * The bundle ships {@see GoogleWalletSaveLinkBuilder} (RS256 JWT signed with the configured service account) and
 * registers it as `nowo_wallet_qr.google_wallet.save_link_builder` when `google_wallet.enabled` is true. A host that
 * signs elsewhere (an HSM, a signing service) implements this interface and registers its service under the same id,
 * or sets `google_wallet.enabled: false` and wires {@see WalletQrService} itself.
 */
interface GoogleWalletSaveLinkBuilderInterface
{
    /**
     * Builds a save link for an existing Google Wallet generic class/object pair.
     *
     * @param GoogleWalletPassReference $reference Pass class and object IDs
     * @param list<string>|null $origins JWT origins (null = configured default)
     *
     * @throws WalletConfigurationException When the signing key is missing or invalid
     *
     * @return string The https://pay.google.com save URL
     */
    public function buildSaveLink(GoogleWalletPassReference $reference, ?array $origins = null): string;

    /**
     * Builds a save link from a raw Google Wallet JWT payload.
     *
     * @param array<string, mixed> $payload Google Wallet "payload" claim
     * @param list<string>|null $origins JWT origins (null = configured default)
     *
     * @throws WalletConfigurationException When the signing key is missing or invalid
     *
     * @return string The https://pay.google.com save URL
     */
    public function buildSaveLinkFromPayload(array $payload, ?array $origins = null): string;
}
