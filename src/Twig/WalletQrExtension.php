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
    /**
     * Creates the Twig extension.
     *
     * @param WalletQrService $walletQrService Wallet QR service
     */
    public function __construct(
        private readonly WalletQrService $walletQrService,
    ) {
    }

    /**
     * Registers `wallet_qr_data_uri()` and `wallet_qr_for_url()`.
     *
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('wallet_qr_data_uri', $this->walletQrDataUri(...)),
            new TwigFunction('wallet_qr_for_url', $this->walletQrForUrl(...)),
        ];
    }

    /**
     * Returns the QR code data URI of a wallet QR.
     *
     * @param WalletQr $walletQr Wallet QR value object
     */
    public function walletQrDataUri(WalletQr $walletQr): string
    {
        return $walletQr->qrCodeDataUri;
    }

    /**
     * Creates a QR code for an http(s) URL labelled with a wallet platform.
     *
     * @param string $platform `android` or `ios`
     * @param string $url URL to encode
     *
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
