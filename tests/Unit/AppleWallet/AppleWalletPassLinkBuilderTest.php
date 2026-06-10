<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\AppleWallet;

use Nowo\WalletQrBundle\AppleWallet\AppleWalletPassLinkBuilder;
use Nowo\WalletQrBundle\Exception\WalletConfigurationException;
use PHPUnit\Framework\TestCase;

final class AppleWalletPassLinkBuilderTest extends TestCase
{
    public function testBuildPassDownloadUrl(): void
    {
        $builder = new AppleWalletPassLinkBuilder('https://example.com/wallet/{pass_id}.pkpass');

        $this->assertSame(
            'https://example.com/wallet/demo-pass-123.pkpass',
            $builder->buildPassDownloadUrl('demo-pass-123'),
        );
    }

    public function testBuildPassDownloadUrlEncodesPassId(): void
    {
        $builder = new AppleWalletPassLinkBuilder('https://example.com/{pass_id}.pkpass');

        $this->assertSame(
            'https://example.com/pass%2Fwith%20space.pkpass',
            $builder->buildPassDownloadUrl('pass/with space'),
        );
    }

    public function testMissingPlaceholderThrows(): void
    {
        $this->expectException(WalletConfigurationException::class);

        $builder = new AppleWalletPassLinkBuilder('https://example.com/pass.pkpass');
        $builder->buildPassDownloadUrl('demo');
    }

    public function testEmptyPatternThrows(): void
    {
        $this->expectException(WalletConfigurationException::class);

        $builder = new AppleWalletPassLinkBuilder('');
        $builder->buildPassDownloadUrl('demo');
    }

    public function testIsConfigured(): void
    {
        $this->assertTrue(AppleWalletPassLinkBuilder::isConfigured('https://example.com/{pass_id}.pkpass'));
        $this->assertFalse(AppleWalletPassLinkBuilder::isConfigured(''));
    }
}
