<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use function is_array;

/**
 * Configuration definition for Wallet QR Bundle.
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * Allowed values for the legacy `qr_code.error_correction` key (same set as nowo_qr_code).
     */
    private const ERROR_CORRECTION_LEVELS = ['low', 'medium', 'quartile', 'high'];

    /**
     * Builds the `nowo_wallet_qr` configuration tree.
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('nowo_wallet_qr');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('google_wallet')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultFalse()
                            ->info('Enable Google Wallet save link generation (Android)')
                        ->end()
                        ->scalarNode('issuer_id')
                            ->defaultValue('')
                            ->info('Google Wallet issuer ID')
                        ->end()
                        ->scalarNode('service_account_json')
                            ->defaultValue('')
                            ->info('Absolute path to the Google Cloud service account JSON key')
                        ->end()
                        ->arrayNode('origins')
                            ->defaultValue([])
                            ->info('Optional JWT origins allowed to initiate save')
                            ->scalarPrototype()->end()
                            ->validate()
                                ->ifTrue(static fn ($v): bool => !is_array($v))
                                ->thenInvalid('google_wallet.origins must be an array')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('apple_wallet')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultFalse()
                            ->info('Enable Apple Wallet pass download URL generation (iOS)')
                        ->end()
                        ->scalarNode('pass_download_url_pattern')
                            ->defaultValue('')
                            ->info('URL pattern with {pass_id} placeholder for .pkpass downloads')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('qr_code')
                    ->addDefaultsIfNotSet()
                    ->info('Deprecated: prefer nowo_qr_code (QrCodeBundle). Values are prepended onto nowo_qr_code for BC.')
                    ->setDeprecated('nowo-tech/wallet-qr-bundle', '3.2', 'The "nowo_wallet_qr.qr_code" option is deprecated: configure "nowo_qr_code" (nowo-tech/qr-code-bundle) instead; the values are still forwarded to it.')
                    ->children()
                        ->integerNode('size')
                            ->defaultValue(300)
                            ->min(64)
                            ->max(1024)
                            ->info('QR code size in pixels (forwarded to nowo_qr_code)')
                        ->end()
                        ->integerNode('margin')
                            ->defaultValue(10)
                            ->min(0)
                            ->max(64)
                            ->info('QR code quiet zone margin in pixels (forwarded to nowo_qr_code)')
                        ->end()
                        ->enumNode('error_correction')
                            ->values(self::ERROR_CORRECTION_LEVELS)
                            ->defaultValue('high')
                            ->info('QR error correction level: low, medium, quartile, high (forwarded to nowo_qr_code)')
                        ->end()
                        ->arrayNode('url_allowlist')
                            ->defaultValue([])
                            ->info('Optional host/URL patterns (forwarded to nowo_qr_code). Empty = any http(s) URL.')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
