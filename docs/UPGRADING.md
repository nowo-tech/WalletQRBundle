# Upgrading

This document describes how to upgrade between versions of Wallet QR Bundle.

## 2.x

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
- **Demos:** `demo/symfony6` removed; use `demo/symfony7`, `demo/symfony8`, or `demo/symfony8-php85`.

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
