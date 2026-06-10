<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit;

use Nowo\WalletQrBundle\NowoWalletQrBundle;
use PHPUnit\Framework\TestCase;

final class NowoWalletQrBundleTest extends TestCase
{
    public function testBundleExtendsSymfonyBundle(): void
    {
        $this->assertSame(NowoWalletQrBundle::class, (new NowoWalletQrBundle())::class);
    }

    public function testBundleHasContainerExtension(): void
    {
        $extension = (new NowoWalletQrBundle())->getContainerExtension();
        $this->assertNotNull($extension);
        $this->assertSame('nowo_wallet_qr', $extension->getAlias());
    }
}
