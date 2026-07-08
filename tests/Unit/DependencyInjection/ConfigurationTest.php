<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\DependencyInjection;

use Nowo\WalletQrBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    private Configuration $configuration;
    private Processor $processor;

    protected function setUp(): void
    {
        $this->configuration = new Configuration();
        $this->processor     = new Processor();
    }

    public function testGetConfigTreeBuilder(): void
    {
        $treeBuilder = $this->configuration->getConfigTreeBuilder();
        $this->assertSame('nowo_wallet_qr', $treeBuilder->buildTree()->getName());
    }

    public function testDefaultConfiguration(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, []);

        $this->assertFalse($config['google_wallet']['enabled']);
        $this->assertSame('', $config['google_wallet']['issuer_id']);
        $this->assertFalse($config['apple_wallet']['enabled']);
        $this->assertSame('', $config['apple_wallet']['pass_download_url_pattern']);
        $this->assertSame(300, $config['qr_code']['size']);
        $this->assertSame(10, $config['qr_code']['margin']);
        $this->assertSame('high', $config['qr_code']['error_correction']);
        $this->assertSame([], $config['qr_code']['url_allowlist']);
    }

    public function testCustomConfiguration(): void
    {
        $configs = [[
            'google_wallet' => [
                'enabled'              => true,
                'issuer_id'            => '3388000000000000000',
                'service_account_json' => '/tmp/google.json',
                'origins'              => ['example.com'],
            ],
            'apple_wallet' => [
                'enabled'                   => true,
                'pass_download_url_pattern' => 'https://example.com/pass/{pass_id}.pkpass',
            ],
            'qr_code' => [
                'size'             => 400,
                'margin'           => 5,
                'error_correction' => 'medium',
            ],
        ]];

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertTrue($config['google_wallet']['enabled']);
        $this->assertSame('3388000000000000000', $config['google_wallet']['issuer_id']);
        $this->assertSame(['example.com'], $config['google_wallet']['origins']);
        $this->assertSame('https://example.com/pass/{pass_id}.pkpass', $config['apple_wallet']['pass_download_url_pattern']);
        $this->assertSame(400, $config['qr_code']['size']);
    }

    public function testQrSizeMustBeWithinBounds(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->processor->processConfiguration($this->configuration, [[
            'qr_code' => ['size' => 32],
        ]]);
    }
}
