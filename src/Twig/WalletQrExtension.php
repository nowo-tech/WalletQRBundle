<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Twig;

use Nowo\WalletQrBundle\Enum\WalletPlatform;
use Nowo\WalletQrBundle\Model\WalletQr;
use Nowo\WalletQrBundle\Service\WalletQrService;
use Twig\Error\RuntimeError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use function array_map;
use function implode;
use function sprintf;

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

    /**
     * @throws RuntimeError when $platform is not a {@see WalletPlatform} value
     */
    public function walletQrForUrl(string $platform, string $url): WalletQr
    {
        $walletPlatform = WalletPlatform::tryFrom($platform);
        if ($walletPlatform === null) {
            throw new RuntimeError(sprintf('wallet_qr_for_url(): unknown wallet platform "%s"; expected one of: %s.', $platform, implode(', ', array_map(static fn (WalletPlatform $p): string => $p->value, WalletPlatform::cases()))));
        }

        return $this->walletQrService->createQrForUrl($walletPlatform, $url);
    }
}
