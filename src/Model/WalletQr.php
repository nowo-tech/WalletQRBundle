<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Model;

/**
 * Wallet save link plus a QR code image encoded as a data URI.
 */
final readonly class WalletQr
{
    /**
     * Creates a wallet QR value object.
     *
     * @param WalletLink $link Wallet link encoded in the QR code
     * @param string $qrCodeDataUri PNG QR code as a `data:image/png;base64,` URI
     */
    public function __construct(
        public WalletLink $link,
        public string $qrCodeDataUri,
    ) {
    }
}
