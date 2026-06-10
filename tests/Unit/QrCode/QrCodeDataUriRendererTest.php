<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\QrCode;

use Nowo\WalletQrBundle\QrCode\QrCodeDataUriRenderer;
use PHPUnit\Framework\TestCase;

final class QrCodeDataUriRendererTest extends TestCase
{
    public function testRenderDataUri(): void
    {
        $renderer = new QrCodeDataUriRenderer(size: 200, margin: 5, errorCorrection: 'medium');
        $dataUri  = $renderer->renderDataUri('https://example.com/wallet/demo');

        $this->assertStringStartsWith('data:image/png;base64,', $dataUri);
    }

    /**
     * @dataProvider errorCorrectionProvider
     */
    public function testRenderDataUriWithErrorCorrectionLevels(string $level): void
    {
        $renderer = new QrCodeDataUriRenderer(size: 120, margin: 2, errorCorrection: $level);
        $dataUri  = $renderer->renderDataUri('https://example.com/wallet/' . $level);

        $this->assertStringStartsWith('data:image/png;base64,', $dataUri);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function errorCorrectionProvider(): iterable
    {
        yield 'low' => ['low'];
        yield 'quartile' => ['quartile'];
    }
}
