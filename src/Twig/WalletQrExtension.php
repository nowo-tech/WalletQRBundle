<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Twig;

use Nowo\WalletQrBundle\Enum\WalletPlatform;
use Nowo\WalletQrBundle\Model\WalletQr;
use Nowo\WalletQrBundle\Service\WalletQrService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig helpers to render wallet QR codes in templates.
 */
final class WalletQrExtension extends AbstractExtension
{
    public function __construct(
        private readonly WalletQrService $walletQrService,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('wallet_qr_data_uri', $this->walletQrDataUri(...)),
            new TwigFunction('wallet_qr_for_url', $this->walletQrForUrl(...)),
        ];
    }

    public function walletQrDataUri(WalletQr $walletQr): string
    {
        return $walletQr->qrCodeDataUri;
    }

    public function walletQrForUrl(string $platform, string $url): WalletQr
    {
        return $this->walletQrService->createQrForUrl(
            WalletPlatform::from($platform),
            $url,
        );
    }
}
