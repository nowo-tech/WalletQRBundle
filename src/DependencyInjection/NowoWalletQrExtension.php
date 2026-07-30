<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\DependencyInjection;

use Nowo\WalletQrBundle\AppleWallet\AppleWalletPassLinkBuilder;
use Nowo\WalletQrBundle\GoogleWallet\GoogleWalletSaveLinkBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use function is_array;

/**
 * Dependency injection extension for the Wallet QR bundle.
 *
 * QR rendering is provided by nowo-tech/qr-code-bundle. Legacy `nowo_wallet_qr.qr_code`
 * keys are prepended onto `nowo_qr_code` for backward compatibility.
 */
final class NowoWalletQrExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        foreach ($container->getExtensionConfig('nowo_wallet_qr') as $config) {
            if (!isset($config['qr_code']) || !is_array($config['qr_code'])) {
                continue;
            }
            $container->prependExtensionConfig('nowo_qr_code', $config['qr_code']);
        }
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter('nowo_wallet_qr.config', $config);

        // Flat parameters kept for BC with apps reading %nowo_wallet_qr.config.qr_code.*%
        $qrCode = $config['qr_code'] ?? [];
        $container->setParameter('nowo_wallet_qr.config.qr_code.size', $qrCode['size'] ?? 300);
        $container->setParameter('nowo_wallet_qr.config.qr_code.margin', $qrCode['margin'] ?? 10);
        $container->setParameter('nowo_wallet_qr.config.qr_code.error_correction', $qrCode['error_correction'] ?? 'high');
        $container->setParameter('nowo_wallet_qr.config.qr_code.url_allowlist', $qrCode['url_allowlist'] ?? []);

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
