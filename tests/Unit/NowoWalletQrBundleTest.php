<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit;

use LogicException;
use Nowo\QrCodeBundle\DependencyInjection\NowoQrCodeExtension;
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

    public function testBuildRequiresQrCodeBundleExtension(): void
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new NowoQrCodeExtension());

        (new NowoWalletQrBundle())->build($container);

        $this->addToAssertionCount(1);
    }

    public function testBuildFailsWithoutQrCodeBundle(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('requires NowoQrCodeBundle');

        (new NowoWalletQrBundle())->build(new ContainerBuilder());
    }
}
