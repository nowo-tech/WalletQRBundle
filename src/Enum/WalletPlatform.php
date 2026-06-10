<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Enum;

/**
 * Target mobile wallet platform for a save link or QR code.
 */
enum WalletPlatform: string
{
    case Android = 'android';
    case Ios     = 'ios';
}
