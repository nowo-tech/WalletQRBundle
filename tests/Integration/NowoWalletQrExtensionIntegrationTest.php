<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Integration;

use Nowo\WalletQrBundle\DependencyInjection\NowoWalletQrExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Integration tests: DI extension registers bundle parameters and services.
 *
 * @covers \Nowo\WalletQrBundle\DependencyInjection\NowoWalletQrExtension
 */
final class NowoWalletQrExtensionIntegrationTest extends TestCase
{
    public function testExtensionLoadsDefaultConfiguration(): void
    {
        $container = new ContainerBuilder();
        (new NowoWalletQrExtension())->load([], $container);

        self::assertTrue($container->hasParameter('nowo_wallet_qr.config'));
        self::assertFalse($container->getParameter('nowo_wallet_qr.google_wallet.enabled'));
        self::assertFalse($container->getParameter('nowo_wallet_qr.apple_wallet.enabled'));
    }

    public function testExtensionRegistersGoogleWalletBuilderWhenConfigured(): void
    {
        $container = new ContainerBuilder();
        (new NowoWalletQrExtension())->load([[
            'google_wallet' => [
                'enabled'              => true,
                'issuer_id'            => '3388000000000000000',
                'service_account_json' => __FILE__,
                'origins'              => ['example.com'],
            ],
        ]], $container);

        self::assertTrue($container->hasDefinition('nowo_wallet_qr.google_wallet.save_link_builder'));
        self::assertTrue($container->getParameter('nowo_wallet_qr.google_wallet.enabled'));
    }
}
