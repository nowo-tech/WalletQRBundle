<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\Exception;

use Nowo\WalletQrBundle\Exception\InvalidWalletQrUrlException;
use PHPUnit\Framework\TestCase;

final class InvalidWalletQrUrlExceptionTest extends TestCase
{
    public function testExceptionMessage(): void
    {
        $exception = new InvalidWalletQrUrlException('URL is not allowed for QR encoding: javascript:alert(1)');

        $this->assertSame('URL is not allowed for QR encoding: javascript:alert(1)', $exception->getMessage());
    }
}
