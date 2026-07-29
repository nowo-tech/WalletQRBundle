<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit;

use Nowo\WalletQrBundle\NowoWalletQrBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class NowoWalletQrBundleTest extends TestCase
{
    public function testBundleName(): void
    {
        $this->assertSame('NowoWalletQrBundle', (new NowoWalletQrBundle())->getName());
    }

    public function testBundleHasContainerExtension(): void
    {
        $extension = (new NowoWalletQrBundle())->getContainerExtension();
        $this->assertSame('nowo_wallet_qr', $extension->getAlias());
    }

    public function testBuild(): void
    {
        $container = new ContainerBuilder();
        (new NowoWalletQrBundle())->build($container);

        $this->addToAssertionCount(1);
    }
}
