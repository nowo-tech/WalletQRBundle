<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\Exception;

use Nowo\WalletQrBundle\Exception\WalletConfigurationException;
use PHPUnit\Framework\TestCase;

final class WalletConfigurationExceptionTest extends TestCase
{
    public function testExceptionMessage(): void
    {
        $exception = new WalletConfigurationException('Wallet config missing');

        $this->assertSame('Wallet config missing', $exception->getMessage());
    }
}
