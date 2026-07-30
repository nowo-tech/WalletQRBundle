<?php

declare(strict_types=1);

/**
 * PHPStan bootstrap: register BC class aliases so analysis sees WalletQr legacy type names.
 */
use Nowo\QrCodeBundle\Exception\InvalidQrUrlException;
use Nowo\QrCodeBundle\QrCode\QrCodeDataUriRenderer;
use Nowo\QrCodeBundle\Security\QrUrlPolicy;
use Nowo\WalletQrBundle\Exception\InvalidWalletQrUrlException;

class_alias(InvalidQrUrlException::class, InvalidWalletQrUrlException::class);
class_alias(QrCodeDataUriRenderer::class, Nowo\WalletQrBundle\QrCode\QrCodeDataUriRenderer::class);
class_alias(QrUrlPolicy::class, Nowo\WalletQrBundle\Security\QrUrlPolicy::class);
