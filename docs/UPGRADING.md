# Upgrading

This document describes how to upgrade between versions of Wallet QR Bundle.

## 2.x

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
