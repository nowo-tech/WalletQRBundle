<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle;

use Nowo\WalletQrBundle\DependencyInjection\NowoWalletQrExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Symfony bundle for Google Wallet and Apple Wallet QR save links.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 * @copyright 2026 Nowo.tech
 */
final class NowoWalletQrBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        if (!$this->extension instanceof ExtensionInterface) {
            $this->extension = new NowoWalletQrExtension();
        }

        return $this->extension;
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }
}
