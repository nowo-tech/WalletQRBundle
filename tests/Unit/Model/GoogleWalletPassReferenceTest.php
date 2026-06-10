<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\Model;

use Nowo\WalletQrBundle\Model\GoogleWalletPassReference;
use PHPUnit\Framework\TestCase;

final class GoogleWalletPassReferenceTest extends TestCase
{
    public function testWithIssuerBuildsQualifiedIds(): void
    {
        $reference = GoogleWalletPassReference::withIssuer(
            '3388000000000000000',
            'MEMBER_001',
            'MEMBER_CLASS',
        );

        $this->assertSame('3388000000000000000.MEMBER_001', $reference->objectId);
        $this->assertSame('3388000000000000000.MEMBER_CLASS', $reference->classId);
    }
}
