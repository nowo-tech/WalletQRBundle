# Installation

## Table of contents

- [Requirements](#requirements)
- [Composer](#composer)
- [Enable the bundle](#enable-the-bundle)
- [Configuration file](#configuration-file)
- [Verify](#verify)
- [Demo](#demo)
- [Symfony Flex recipe](#symfony-flex-recipe)

## Requirements

- PHP 8.2 or higher
- Symfony 7.x or 8.x
- **[`nowo-tech/qr-code-bundle`](https://github.com/nowo-tech/QrCodeBundle)** (required; installed automatically by Composer)
- PHP **gd** (PNG QR rendering)
- Google Cloud service account JSON key (for Google Wallet / Android)
- HTTPS endpoint serving `.pkpass` files (for Apple Wallet / iOS)

## Composer

```bash
composer require nowo-tech/wallet-qr-bundle
```

Composer installs the required [`nowo-tech/qr-code-bundle`](https://github.com/nowo-tech/QrCodeBundle) and `firebase/php-jwt` transitively. Do not remove `qr-code-bundle` from the lockfile — Wallet QR cannot run without it.

## Enable the bundle

Symfony Flex registers **both** `NowoQrCodeBundle` and `NowoWalletQrBundle` (see the recipe). Manual registration:

```php
// config/bundles.php
return [
    // ...
    Nowo\QrCodeBundle\NowoQrCodeBundle::class => ['all' => true], // required
    Nowo\WalletQrBundle\NowoWalletQrBundle::class => ['all' => true],
];
```

## Configuration file

Create `config/packages/nowo_wallet_qr.yaml` (see [Configuration](CONFIGURATION.md)).

## Verify

```bash
php bin/console debug:container WalletQrService
```

## Demo

See [Demo with FrankenPHP](DEMO-FRANKENPHP.md).

## Symfony Flex recipe

When using Symfony Flex, the recipe at `.symfony/recipe/nowo-tech/wallet-qr-bundle/0.1/` copies:

- `config/packages/nowo_wallet_qr.yaml` — default bundle configuration

See `post-install.txt` in the recipe for next steps after `composer require`.
