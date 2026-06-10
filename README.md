# Wallet QR Bundle

[![CI](https://github.com/nowo-tech/wallet-qr-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/nowo-tech/wallet-qr-bundle/actions/workflows/ci.yml)
[![Packagist Version](https://img.shields.io/packagist/v/nowo-tech/wallet-qr-bundle.svg?style=flat)](https://packagist.org/packages/nowo-tech/wallet-qr-bundle)
[![Packagist Downloads](https://img.shields.io/packagist/dt/nowo-tech/wallet-qr-bundle.svg)](https://packagist.org/packages/nowo-tech/wallet-qr-bundle)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php)](https://php.net)
[![Symfony](https://img.shields.io/badge/Symfony-6%20%7C%207%20%7C%208-000000?logo=symfony)](https://symfony.com)
[![GitHub stars](https://img.shields.io/github/stars/nowo-tech/wallet-qr-bundle.svg?style=social&label=Star)](https://github.com/nowo-tech/wallet-qr-bundle)
[![Coverage](https://img.shields.io/badge/Coverage-100%25-brightgreen)](#tests-and-coverage)

> ⭐ **Found this useful?** Give it a star on GitHub! It helps us maintain and improve the project.

**Symfony bundle to generate Google Wallet (Android) and Apple Wallet (iOS) save links with QR codes.**

> 📋 **Compatible with Symfony 6.x, 7.x, and 8.x**

## Features

- ✅ Google Wallet **Add to Google Wallet** save links (signed JWT)
- ✅ Apple Wallet `.pkpass` download URL builder for iOS QR codes
- ✅ PNG QR codes as data URIs (Twig helpers included)
- ✅ Pair generation for Android + iOS in one call
- ✅ Symfony configuration under `nowo_wallet_qr`
- ✅ Demo apps for Symfony 6, 7, and 8 (FrankenPHP)

## Quick start

```bash
composer require nowo-tech/wallet-qr-bundle endroid/qr-code firebase/php-jwt
```

```yaml
# config/packages/nowo_wallet_qr.yaml
nowo_wallet_qr:
    google_wallet:
        enabled: true
        issuer_id: '%env(GOOGLE_WALLET_ISSUER_ID)%'
        service_account_json: '%kernel.project_dir%/config/google-wallet-service-account.json'
        origins: ['www.example.com']
    apple_wallet:
        enabled: true
        pass_download_url_pattern: 'https://www.example.com/wallet/{pass_id}.pkpass'
    qr_code:
        size: 300
```

```php
use Nowo\WalletQrBundle\Model\GoogleWalletPassReference;
use Nowo\WalletQrBundle\Service\WalletQrService;

$reference = GoogleWalletPassReference::withIssuer(
    $issuerId,
    objectSuffix: 'MEMBER_001',
    classSuffix: 'MEMBER_CLASS',
);

$pair = $walletQrService->createWalletQrPair($reference, applePassId: 'MEMBER_001');
// $pair['android']->qrCodeDataUri, $pair['ios']->qrCodeDataUri
```

## Documentation

- [Installation](docs/INSTALLATION.md)
- [Configuration](docs/CONFIGURATION.md)
- [Usage](docs/USAGE.md)
- [Contributing](docs/CONTRIBUTING.md)
- [Changelog](docs/CHANGELOG.md)
- [Upgrading](docs/UPGRADING.md)
- [Release process](docs/RELEASE.md)
- [Security](docs/SECURITY.md)
- [Engram](docs/ENGRAM.md)
- [Spec-driven development](docs/SPEC-DRIVEN-DEVELOPMENT.md)

### Additional documentation

- [Demo with FrankenPHP](docs/DEMO-FRANKENPHP.md)

## Development

```bash
make up
make test
make test-coverage
make release-check
```

Demos: `make -C demo/symfony8 up`

## Tests and coverage

| Language | Coverage |
| --- | --- |
| PHP | ~100% lines |

Run `make test-coverage` for the detailed report.

## License

MIT — see [LICENSE](LICENSE).
