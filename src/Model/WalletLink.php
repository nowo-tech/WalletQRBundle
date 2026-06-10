<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Model;

use Nowo\WalletQrBundle\Enum\WalletPlatform;

/**
 * A wallet save or download URL for a specific mobile platform.
 */
final readonly class WalletLink
{
    public function __construct(
        public WalletPlatform $platform,
        public string $url,
    ) {
    }
}
