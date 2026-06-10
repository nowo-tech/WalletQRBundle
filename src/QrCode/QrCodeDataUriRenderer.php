<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\QrCode;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Renders wallet URLs as PNG QR codes encoded as data URIs.
 */
final class QrCodeDataUriRenderer
{
    public function __construct(
        private readonly int $size = 300,
        private readonly int $margin = 10,
        private readonly string $errorCorrection = 'high',
    ) {
    }

    public function renderDataUri(string $content): string
    {
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $content,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: $this->resolveErrorCorrectionLevel(),
            size: $this->size,
            margin: $this->margin,
        );

        $result = $builder->build();

        return $result->getDataUri();
    }

    private function resolveErrorCorrectionLevel(): ErrorCorrectionLevel
    {
        return match (strtolower($this->errorCorrection)) {
            'low'      => ErrorCorrectionLevel::Low,
            'medium'   => ErrorCorrectionLevel::Medium,
            'quartile' => ErrorCorrectionLevel::Quartile,
            default    => ErrorCorrectionLevel::High,
        };
    }
}
