# Installation

## Requirements

- PHP 8.1 or higher
- Symfony 6.x, 7.x, or 8.x
- Google Cloud service account JSON key (for Google Wallet / Android)
- HTTPS endpoint serving `.pkpass` files (for Apple Wallet / iOS)

## Composer

```bash
composer require nowo-tech/wallet-qr-bundle
```

The bundle declares `endroid/qr-code` and `firebase/php-jwt` as runtime dependencies.

## Enable the bundle

Symfony Flex registers the bundle automatically. Manual registration:

```php
// config/bundles.php
return [
    // ...
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
