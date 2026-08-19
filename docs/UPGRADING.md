# Upgrading

This document describes how to upgrade between versions of Wallet QR Bundle.

## Table of contents

- [3.x](#3x)
- [2.x](#2x)
- [1.x](#1x)

## 3.x

### 3.0.4

From **3.0.3** — No application upgrade steps. Documentation-only release.

```bash
composer update nowo-tech/wallet-qr-bundle
```

### 3.0.3

From **3.0.2** — Review production SSRF config. Flex recipe `when@prod` ships a `qr.url_allowlist` placeholder. Replace with allowed host patterns before generating wallet QR codes from user URLs.

```bash
composer update nowo-tech/wallet-qr-bundle
php bin/console cache:clear
```

### 3.0.2

- **No action required.** Demos only: Hot Reload Bundle `^1.4`; Symfony 8 is the only shipped demo (Symfony 6/7 demo apps removed).

```bash
composer update nowo-tech/wallet-qr-bundle
```

### 3.0.1

- **No action required.** Adds a unit test for the legacy `nowo_wallet_qr.qr_code` → `nowo_qr_code` prepend bridge when configs omit or misuse `qr_code`. Behaviour unchanged from **3.0.0**.

```bash
composer update nowo-tech/wallet-qr-bundle
```

### 3.0.0

**Breaking:** QR generation moved to the required dependency [`nowo-tech/qr-code-bundle`](https://github.com/nowo-tech/QrCodeBundle).

1. Update the package (QrCodeBundle is installed automatically):

```bash
composer update nowo-tech/wallet-qr-bundle
```

2. Enable **both** bundles (Flex recipe registers them; otherwise add manually):

```php
// config/bundles.php
Nowo\QrCodeBundle\NowoQrCodeBundle::class => ['all' => true],
Nowo\WalletQrBundle\NowoWalletQrBundle::class => ['all' => true],
```

3. Prefer QR options under `nowo_qr_code`. Existing `nowo_wallet_qr.qr_code` keys still work (prepended onto `nowo_qr_code`).

```yaml
# config/packages/nowo_qr_code.yaml
nowo_qr_code:
    size: 300
    margin: 10
    error_correction: high
    url_allowlist: []
```

4. If you construct services manually, inject `Nowo\QrCodeBundle\Service\QrCodeService` (built with `ProfileResolver`). Deprecated WalletQr aliases remain for `QrCodeDataUriRenderer`, `QrUrlPolicy`, and `InvalidWalletQrUrlException`.

5. For QR codes without wallet links, use `QrCodeService` / Twig `qr_code_data_uri` / `qr_code_for_url` / `<twig:NowoQrCode>` from QrCodeBundle.

## 2.x

### 2.1.5

- **No action required** for typical applications using DI defaults. Flat container parameters `nowo_wallet_qr.config.qr_code.*` are now registered so Symfony can resolve `%nowo_wallet_qr.config.qr_code.size%` (and related keys) from `services.yaml`.
- **CI / tests:** PHPUnit and GitHub Actions set `SYMFONY_DEPRECATIONS_HELPER=max[direct]=0` (REQ-SF-005). Applications should treat new direct Symfony deprecations as regressions when upgrading.
- **Demos:** Symfony 8 demos use FrankenPHP **PHP 8.5** images (`dunglas/frankenphp:1-php8.5-alpine`); images install PHP **gd** for QR PNG rendering.

```bash
composer update nowo-tech/wallet-qr-bundle
```

### 2.1.4

- **No action required** for applications using the Packagist package. Demo-only: FrankenPHP mode is controlled with `FRANKENPHP_MODE` (`classic` or `worker`). See [DEMO-FRANKENPHP.md](DEMO-FRANKENPHP.md) if you run or copy the bundled demos.

### 2.1.3

- **No action required** for application code. Maintenance release: git hygiene (REQ-GIT-001), Code of Conduct, CI docs, and lock sync; no API or runtime changes.
- **Contributors:** run `make setup-hooks` so `commit-msg` strips accidental Cursor co-author trailers. See [GITHUB_CI.md](GITHUB_CI.md).

### 2.1.2

- **No action required.** Maintenance release: dev lock sync and `.gitignore` only; no API or runtime changes.

### 2.1.1

- **No action required.** Test-only patch; runtime behaviour unchanged since 2.1.0.

### 2.1.0

- **URL validation:** `createQrForUrl` and `wallet_qr_for_url` now reject non-http(s) schemes (`javascript:`, `data:`, etc.) with `InvalidWalletQrUrlException`. If you passed only valid https URLs, no change is required.
- **Optional allowlist:** set `qr_code.url_allowlist` to restrict which hosts/URLs can be encoded (see [Configuration](CONFIGURATION.md)).
- **Manual instantiation:** if you construct `WalletQrService` yourself (outside DI), pass `QrUrlPolicy` as the second constructor argument.
- **Demos:** `demo/symfony8` removed; use `demo/symfony8`, or `demo/symfony8-php85`.

### 2.0.0

- **Breaking:** PHP **8.2+** and Symfony **^7.0 || ^8.0** are now required.
- Upgrade your application to PHP 8.2+ and Symfony 7 or 8 before updating the bundle.
- Stay on **1.1.x** if you still run PHP 8.1 or Symfony 6.

## 1.x

### 1.1.0

- **No breaking changes** for applications consuming the bundle from Packagist. The public API in `WalletQrService`, Twig helpers, and configuration keys is unchanged.
- **CI / demos**: Symfony 7 demo now targets **7.4**; Symfony 8 demos target **8.1**. This does not affect runtime requirements of the bundle itself (`^6.0 || ^7.0 || ^8.0`).
- **Documentation**: GitHub links now point to `nowo-tech/WalletQrBundle`. No action required unless you copied old URLs into your project docs.
- **Contributors**: optional pre-commit hooks via `make setup-hooks` (runs `cs-check` and `test` before each commit).

### 1.0.0

First stable release.

- **Requirements:** PHP >= 8.1, Symfony ^6.0 || ^7.0 || ^8.0.
- Google Wallet save links (JWT), Apple Wallet pass URL builder, QR data URI rendering, and Twig helpers.
