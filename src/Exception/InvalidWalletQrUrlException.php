<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Exception;

use Nowo\QrCodeBundle\Exception\InvalidQrUrlException;

/*
 * @deprecated since WalletQrBundle 3.0 — use {@see InvalidQrUrlException} from nowo-tech/qr-code-bundle
 */
class_alias(InvalidQrUrlException::class, InvalidWalletQrUrlException::class);
