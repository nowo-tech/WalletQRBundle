<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\DependencyInjection;

use Nowo\WalletQrBundle\AppleWallet\AppleWalletPassLinkBuilder;
use Nowo\WalletQrBundle\GoogleWallet\GoogleWalletSaveLinkBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Dependency injection extension for the Wallet QR bundle.
 */
class NowoWalletQrExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter('nowo_wallet_qr.config', $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $this->registerGoogleWalletBuilder($container, $config);
        $this->registerAppleWalletBuilder($container, $config);
    }

    public function getAlias(): string
    {
        return 'nowo_wallet_qr';
    }

    /**
     * @param array<string, mixed> $config
     */
    private function registerGoogleWalletBuilder(ContainerBuilder $container, array $config): void
    {
        $googleConfig = $config['google_wallet'] ?? [];
        $enabled      = (bool) ($googleConfig['enabled'] ?? false);
        $issuerId     = (string) ($googleConfig['issuer_id'] ?? '');
        $jsonPath     = (string) ($googleConfig['service_account_json'] ?? '');

        if (!$enabled || !GoogleWalletSaveLinkBuilder::isConfigured($issuerId, $jsonPath)) {
            $container->setParameter('nowo_wallet_qr.google_wallet.enabled', false);

            return;
        }

        $definition = new Definition(GoogleWalletSaveLinkBuilder::class, [
            $issuerId,
            $jsonPath,
            $googleConfig['origins'] ?? [],
        ]);
        $definition->setAutowired(false);
        $definition->setPublic(false);
        $container->setDefinition('nowo_wallet_qr.google_wallet.save_link_builder', $definition);
        $container->setParameter('nowo_wallet_qr.google_wallet.enabled', true);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function registerAppleWalletBuilder(ContainerBuilder $container, array $config): void
    {
        $appleConfig = $config['apple_wallet'] ?? [];
        $enabled     = (bool) ($appleConfig['enabled'] ?? false);
        $pattern     = (string) ($appleConfig['pass_download_url_pattern'] ?? '');

        if (!$enabled || !AppleWalletPassLinkBuilder::isConfigured($pattern)) {
            $container->setParameter('nowo_wallet_qr.apple_wallet.enabled', false);

            return;
        }

        $definition = new Definition(AppleWalletPassLinkBuilder::class, [$pattern]);
        $definition->setAutowired(false);
        $definition->setPublic(false);
        $container->setDefinition('nowo_wallet_qr.apple_wallet.pass_link_builder', $definition);
        $container->setParameter('nowo_wallet_qr.apple_wallet.enabled', true);
    }
}
