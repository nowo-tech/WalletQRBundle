<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Model;

/**
 * Wallet save link plus a QR code image encoded as a data URI.
 */
final readonly class WalletQr
{
    public function __construct(
        public WalletLink $link,
        public string $qrCodeDataUri,
    ) {
    }
}
