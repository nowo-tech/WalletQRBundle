<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Exception;

use RuntimeException;

/**
 * Thrown when a URL cannot be encoded into a wallet QR code.
 */
final class InvalidWalletQrUrlException extends RuntimeException
{
}
