<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Security;

use Nowo\QrCodeBundle\Security\QrUrlPolicy as BaseQrUrlPolicy;

/*
 * @deprecated since WalletQrBundle 3.0 — use {@see BaseQrUrlPolicy} from nowo-tech/qr-code-bundle
 */
class_alias(BaseQrUrlPolicy::class, QrUrlPolicy::class);
