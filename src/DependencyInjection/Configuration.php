<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use function is_array;
use function is_string;

/**
 * Configuration definition for Wallet QR Bundle.
 */
class Configuration implements ConfigurationInterface
{
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
                    ->children()
                        ->integerNode('size')
                            ->defaultValue(300)
                            ->min(64)
                            ->max(1024)
                            ->info('QR code size in pixels')
                        ->end()
                        ->integerNode('margin')
                            ->defaultValue(10)
                            ->min(0)
                            ->max(64)
                            ->info('QR code quiet zone margin in pixels')
                        ->end()
                        ->scalarNode('error_correction')
                            ->defaultValue('high')
                            ->info('QR error correction level: low, medium, quartile, high')
                            ->validate()
                                ->ifTrue(static fn ($v): bool => !is_string($v))
                                ->thenInvalid('qr_code.error_correction must be a string')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
