<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\DependencyInjection;

use Nowo\WalletQrBundle\DependencyInjection\NowoWalletQrExtension;
use Nowo\WalletQrBundle\Service\WalletQrService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function dirname;

final class NowoWalletQrExtensionTest extends TestCase
{
    private NowoWalletQrExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new NowoWalletQrExtension();
    }

    public function testGetAlias(): void
    {
        $this->assertSame('nowo_wallet_qr', $this->extension->getAlias());
    }

    public function testLoadRegistersCoreServices(): void
    {
        $container = new ContainerBuilder();
        $this->extension->load([], $container);

        $this->assertTrue($container->hasDefinition(WalletQrService::class));
        $this->assertTrue($container->hasParameter('nowo_wallet_qr.config'));
        $this->assertSame(300, $container->getParameter('nowo_wallet_qr.config.qr_code.size'));
        $this->assertSame([], $container->getParameter('nowo_wallet_qr.config.qr_code.url_allowlist'));
    }

    public function testLoadRegistersGoogleWalletBuilderWhenEnabled(): void
    {
        $container = new ContainerBuilder();
        $fixture   = dirname(__DIR__, 2) . '/fixtures/google-service-account.json';

        $this->extension->load([[
            'google_wallet' => [
                'enabled'              => true,
                'issuer_id'            => '3388000000000000000',
                'service_account_json' => $fixture,
            ],
        ]], $container);

        $this->assertTrue($container->hasDefinition('nowo_wallet_qr.google_wallet.save_link_builder'));
        $this->assertTrue($container->getParameter('nowo_wallet_qr.google_wallet.enabled'));
    }

    public function testLoadRegistersAppleWalletBuilderWhenEnabled(): void
    {
        $container = new ContainerBuilder();

        $this->extension->load([[
            'apple_wallet' => [
                'enabled'                   => true,
                'pass_download_url_pattern' => 'https://example.com/pass/{pass_id}.pkpass',
            ],
        ]], $container);

        $this->assertTrue($container->hasDefinition('nowo_wallet_qr.apple_wallet.pass_link_builder'));
        $this->assertTrue($container->getParameter('nowo_wallet_qr.apple_wallet.enabled'));
    }
}
