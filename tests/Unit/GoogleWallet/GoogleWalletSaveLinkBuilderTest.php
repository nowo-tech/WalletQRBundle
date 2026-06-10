<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\GoogleWallet;

use Nowo\WalletQrBundle\Exception\WalletConfigurationException;
use Nowo\WalletQrBundle\GoogleWallet\GoogleWalletSaveLinkBuilder;
use Nowo\WalletQrBundle\Model\GoogleWalletPassReference;
use PHPUnit\Framework\TestCase;

use function dirname;
use function strlen;

final class GoogleWalletSaveLinkBuilderTest extends TestCase
{
    private string $fixturePath;

    protected function setUp(): void
    {
        $this->fixturePath = dirname(__DIR__, 2) . '/fixtures/google-service-account.json';
    }

    public function testBuildSaveLink(): void
    {
        $builder = new GoogleWalletSaveLinkBuilder(
            '3388000000000000000',
            $this->fixturePath,
            ['example.com'],
        );

        $reference = GoogleWalletPassReference::withIssuer(
            '3388000000000000000',
            'OBJECT_SUFFIX',
            'CLASS_SUFFIX',
        );

        $url = $builder->buildSaveLink($reference);

        $this->assertStringStartsWith('https://pay.google.com/gp/v/save/', $url);
        $this->assertGreaterThan(100, strlen($url));
    }

    public function testBuildSaveLinkFromPayload(): void
    {
        $builder = new GoogleWalletSaveLinkBuilder('3388000000000000000', $this->fixturePath);
        $url     = $builder->buildSaveLinkFromPayload([
            'genericObjects' => [[
                'id'      => '3388000000000000000.OBJECT',
                'classId' => '3388000000000000000.CLASS',
            ]],
        ]);

        $this->assertStringStartsWith('https://pay.google.com/gp/v/save/', $url);
    }

    public function testMissingServiceAccountFileThrows(): void
    {
        $this->expectException(WalletConfigurationException::class);

        $builder = new GoogleWalletSaveLinkBuilder('3388000000000000000', '/missing/file.json');
        $builder->buildSaveLinkFromPayload(['genericObjects' => []]);
    }

    public function testIsConfigured(): void
    {
        $this->assertTrue(GoogleWalletSaveLinkBuilder::isConfigured('3388000000000000000', $this->fixturePath));
        $this->assertFalse(GoogleWalletSaveLinkBuilder::isConfigured('', $this->fixturePath));
    }
}
