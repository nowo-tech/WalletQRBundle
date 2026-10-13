<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\DependencyInjection;

use Nowo\WalletQrBundle\DependencyInjection\NowoWalletQrExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;

final class NowoWalletQrExtensionDisabledIntegrationsTest extends TestCase
{
    private NowoWalletQrExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new NowoWalletQrExtension();
    }

    public function testGoogleWalletEnabledWithoutIssuerFailsAtCompile(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('nowo_wallet_qr.google_wallet is enabled but issuer_id is empty');

        $this->extension->load([[
            'google_wallet' => [
                'enabled'              => true,
                'issuer_id'            => '',
                'service_account_json' => '/path/sa.json',
            ],
        ]], new ContainerBuilder());
    }

    public function testGoogleWalletEnabledWithoutServiceAccountFailsAtCompile(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('service_account_json is empty');

        $this->extension->load([[
            'google_wallet' => [
                'enabled'   => true,
                'issuer_id' => '3388000000012345678',
            ],
        ]], new ContainerBuilder());
    }

    public function testAppleWalletPatternWithoutPassIdFailsAtCompile(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('("https://example.com/pass.pkpass") has no "{pass_id}" placeholder');

        $this->extension->load([[
            'apple_wallet' => [
                'enabled'                   => true,
                'pass_download_url_pattern' => 'https://example.com/pass.pkpass',
            ],
        ]], new ContainerBuilder());
    }

    public function testAppleWalletEmptyPatternFailsAtCompile(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('pass_download_url_pattern is empty');

        $this->extension->load([[
            'apple_wallet' => ['enabled' => true, 'pass_download_url_pattern' => '  '],
        ]], new ContainerBuilder());
    }

    public function testAppleWalletEnvPatternIsValidatedAtRuntime(): void
    {
        // As in MergeExtensionConfigurationPass: %env()% reaches load() as an env placeholder.
        $bag       = new EnvPlaceholderParameterBag();
        $container = new ContainerBuilder($bag);
        $pattern   = $bag->get('env(APPLE_WALLET_PATTERN)');
        $this->assertIsString($pattern);

        $this->extension->load([[
            'apple_wallet' => ['enabled' => true, 'pass_download_url_pattern' => $pattern],
        ]], $container);

        $this->assertTrue($container->getParameter('nowo_wallet_qr.apple_wallet.enabled'));
        $this->assertTrue($container->hasDefinition('nowo_wallet_qr.apple_wallet.pass_link_builder'));
    }

    public function testDisabledIntegrationsIgnoreIncompleteSettings(): void
    {
        $container = new ContainerBuilder();
        $this->extension->load([[
            'google_wallet' => ['enabled' => false, 'issuer_id' => ''],
            'apple_wallet'  => ['enabled' => false, 'pass_download_url_pattern' => 'https://example.com/pass.pkpass'],
        ]], $container);

        $this->assertFalse($container->getParameter('nowo_wallet_qr.google_wallet.enabled'));
        $this->assertFalse($container->getParameter('nowo_wallet_qr.apple_wallet.enabled'));
    }

    public function testIntegrationsDisabledByDefault(): void
    {
        $container = new ContainerBuilder();
        $this->extension->load([], $container);

        $this->assertFalse($container->getParameter('nowo_wallet_qr.google_wallet.enabled'));
        $this->assertFalse($container->getParameter('nowo_wallet_qr.apple_wallet.enabled'));
    }
}
