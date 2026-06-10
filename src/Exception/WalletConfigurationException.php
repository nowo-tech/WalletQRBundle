<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Exception;

use RuntimeException;

/**
 * Thrown when wallet link generation cannot proceed due to missing or invalid configuration.
 */
final class WalletConfigurationException extends RuntimeException
{
}
