# Wallet QR Bundle

[![CI](https://github.com/nowo-tech/WalletQrBundle/actions/workflows/ci.yml/badge.svg)](https://github.com/nowo-tech/WalletQrBundle/actions/workflows/ci.yml) [![Packagist Version](https://img.shields.io/packagist/v/nowo-tech/wallet-qr-bundle.svg?style=flat)](https://packagist.org/packages/nowo-tech/wallet-qr-bundle) [![Packagist Downloads](https://img.shields.io/packagist/dt/nowo-tech/wallet-qr-bundle.svg)](https://packagist.org/packages/nowo-tech/wallet-qr-bundle) [![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE) [![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php)](https://php.net) [![Symfony](https://img.shields.io/badge/Symfony-7.4%20%7C%208.0%20%7C%208.1%2B-000000?logo=symfony)](https://symfony.com) [![GitHub stars](https://img.shields.io/github/stars/nowo-tech/wallet-qr-bundle.svg?style=social&label=Star)](https://github.com/nowo-tech/WalletQrBundle) [![Coverage](https://img.shields.io/badge/Coverage-100%25-brightgreen)](#tests-and-coverage)

> ⭐ **Found this useful?** Give it a star on GitHub! It helps us maintain and improve the project.
> 📋 **Requires PHP 8.2+ and Symfony 7.x or 8.x** (CI matrix includes Symfony **7.4**, **8.0**, **8.1**)

**FrankenPHP demos:** runtime is selected with **`FRANKENPHP_MODE`** (`worker` default, or `classic` for per-request PHP / hot-reload). See [docs/DEMO-FRANKENPHP.md](docs/DEMO-FRANKENPHP.md).
**Symfony bundle to generate Google Wallet (Android) and Apple Wallet (iOS) save links with QR codes.**

![FrankenPHP Friendly Worker Mode](docs/images/frankenphp-friendly.png)

This bundle is **FrankenPHP worker mode friendly**.

## Features

- ✅ Google Wallet **Add to Google Wallet** save links (signed JWT)
- ✅ Apple Wallet `.pkpass` download URL builder for iOS QR codes
- ✅ PNG QR codes as data URIs (Twig helpers included)
- ✅ Pair generation for Android + iOS in one call
- ✅ Symfony configuration under `nowo_wallet_qr`
- ✅ URL validation for custom QR links (`QrUrlPolicy`; optional host allowlist)
- ✅ Demo apps for Symfony 7 and 8 (FrankenPHP)

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

## Development

```bash
make up
make test
make test-coverage
make demo-smoke
make release-check
```

Demos: `make -C demo/symfony8 up`

## Documentation

- [Installation](docs/INSTALLATION.md)
- [Configuration](docs/CONFIGURATION.md)
- [Usage](docs/USAGE.md)
- [Contributing](docs/CONTRIBUTING.md)
- [Code of Conduct](CODE_OF_CONDUCT.md)
- [Changelog](docs/CHANGELOG.md)
- [Upgrading](docs/UPGRADING.md)
- [Release](docs/RELEASE.md)
- [Security](docs/SECURITY.md)
- [Engram](docs/ENGRAM.md)
- [Spec-driven development](docs/SPEC-DRIVEN-DEVELOPMENT.md)
- [GitHub Spec Kit](docs/SPEC-KIT.md)

### Additional documentation

- [GitHub Actions CI requirements](docs/GITHUB_CI.md)
- [Demo with FrankenPHP](docs/DEMO-FRANKENPHP.md)

## Tests and coverage

| Language | Coverage |
| --- | --- |
| PHP | ~100% lines |

Run `make test-coverage` for the detailed report.

## License

MIT — see [LICENSE](LICENSE).
