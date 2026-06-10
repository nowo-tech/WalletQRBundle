<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\DependencyInjection;

use Nowo\WalletQrBundle\DependencyInjection\NowoWalletQrExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class NowoWalletQrExtensionDisabledIntegrationsTest extends TestCase
{
    private NowoWalletQrExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new NowoWalletQrExtension();
    }

    public function testGoogleWalletDisabledWhenMissingCredentials(): void
    {
        $container = new ContainerBuilder();

        $this->extension->load([[
            'google_wallet' => [
                'enabled'   => true,
                'issuer_id' => '',
            ],
        ]], $container);

        $this->assertFalse($container->getParameter('nowo_wallet_qr.google_wallet.enabled'));
        $this->assertFalse($container->hasDefinition('nowo_wallet_qr.google_wallet.save_link_builder'));
    }

    public function testAppleWalletDisabledWhenPatternInvalid(): void
    {
        $container = new ContainerBuilder();

        $this->extension->load([[
            'apple_wallet' => [
                'enabled'                   => true,
                'pass_download_url_pattern' => 'https://example.com/pass.pkpass',
            ],
        ]], $container);

        $this->assertFalse($container->getParameter('nowo_wallet_qr.apple_wallet.enabled'));
        $this->assertFalse($container->hasDefinition('nowo_wallet_qr.apple_wallet.pass_link_builder'));
    }

    public function testIntegrationsDisabledByDefault(): void
    {
        $container = new ContainerBuilder();
        $this->extension->load([], $container);

        $this->assertFalse($container->getParameter('nowo_wallet_qr.google_wallet.enabled'));
        $this->assertFalse($container->getParameter('nowo_wallet_qr.apple_wallet.enabled'));
    }
}
