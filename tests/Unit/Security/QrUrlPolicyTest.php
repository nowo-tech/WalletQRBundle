<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\Security;

use Nowo\WalletQrBundle\Exception\InvalidWalletQrUrlException;
use Nowo\WalletQrBundle\Security\QrUrlPolicy;
use PHPUnit\Framework\TestCase;

final class QrUrlPolicyTest extends TestCase
{
    public function testAllowsHttpsUrl(): void
    {
        $policy = new QrUrlPolicy();

        self::assertTrue($policy->isAllowed('https://example.com/save'));
    }

    public function testBlocksJavascriptUrl(): void
    {
        $policy = new QrUrlPolicy();

        self::assertFalse($policy->isAllowed('javascript:alert(1)'));
    }

    public function testBlocksDataUrl(): void
    {
        $policy = new QrUrlPolicy();

        self::assertFalse($policy->isAllowed('data:text/html,<script>alert(1)</script>'));
    }

    public function testHostAllowlist(): void
    {
        $policy = new QrUrlPolicy(['example.com']);

        self::assertTrue($policy->isAllowed('https://example.com/path'));
        self::assertFalse($policy->isAllowed('https://evil.com/path'));
    }

    public function testAssertAllowedThrows(): void
    {
        $policy = new QrUrlPolicy();

        $this->expectException(InvalidWalletQrUrlException::class);
        $policy->assertAllowed('javascript:alert(1)');
    }
}
