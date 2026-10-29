<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Model;

use Nowo\WalletQrBundle\Enum\WalletPlatform;

/**
 * A wallet save or download URL for a specific mobile platform.
 */
final readonly class WalletLink
{
    /**
     * Creates a wallet link value object.
     *
     * @param WalletPlatform $platform Target mobile platform
     * @param string $url Save or download URL
     */
    public function __construct(
        public WalletPlatform $platform,
        public string $url,
    ) {
    }
}
