<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\QrCode;

use Nowo\QrCodeBundle\QrCode\QrCodeDataUriRenderer as BaseQrCodeDataUriRenderer;

/*
 * @deprecated since WalletQrBundle 3.0 — use {@see BaseQrCodeDataUriRenderer} from nowo-tech/qr-code-bundle
 */
class_alias(BaseQrCodeDataUriRenderer::class, QrCodeDataUriRenderer::class);
