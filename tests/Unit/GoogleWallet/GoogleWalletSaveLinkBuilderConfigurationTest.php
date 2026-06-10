<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\GoogleWallet;

use Nowo\WalletQrBundle\Exception\WalletConfigurationException;
use Nowo\WalletQrBundle\GoogleWallet\GoogleWalletSaveLinkBuilder;
use PHPUnit\Framework\TestCase;

final class GoogleWalletSaveLinkBuilderConfigurationTest extends TestCase
{
    public function testMissingIssuerThrows(): void
    {
        $this->expectException(WalletConfigurationException::class);

        $builder = new GoogleWalletSaveLinkBuilder('', '/tmp/file.json');
        $builder->buildSaveLinkFromPayload(['genericObjects' => []]);
    }

    public function testMissingServiceAccountPathThrows(): void
    {
        $this->expectException(WalletConfigurationException::class);

        $builder = new GoogleWalletSaveLinkBuilder('3388000000000000000', '');
        $builder->buildSaveLinkFromPayload(['genericObjects' => []]);
    }

    public function testInvalidServiceAccountJsonThrows(): void
    {
        $path = sys_get_temp_dir() . '/wallet-qr-invalid.json';
        file_put_contents($path, 'not-json');

        try {
            $this->expectException(WalletConfigurationException::class);
            $builder = new GoogleWalletSaveLinkBuilder('3388000000000000000', $path);
            $builder->buildSaveLinkFromPayload(['genericObjects' => []]);
        } finally {
            @unlink($path);
        }
    }

    public function testMissingClientEmailThrows(): void
    {
        $path = sys_get_temp_dir() . '/wallet-qr-no-email.json';
        file_put_contents($path, '{"private_key":"test"}');

        try {
            $this->expectException(WalletConfigurationException::class);
            $builder = new GoogleWalletSaveLinkBuilder('3388000000000000000', $path);
            $builder->buildSaveLinkFromPayload(['genericObjects' => []]);
        } finally {
            @unlink($path);
        }
    }

    public function testMissingPrivateKeyThrows(): void
    {
        $path = sys_get_temp_dir() . '/wallet-qr-no-key.json';
        file_put_contents($path, '{"client_email":"test@example.com"}');

        try {
            $this->expectException(WalletConfigurationException::class);
            $builder = new GoogleWalletSaveLinkBuilder('3388000000000000000', $path);
            $builder->buildSaveLinkFromPayload(['genericObjects' => []]);
        } finally {
            @unlink($path);
        }
    }
}
