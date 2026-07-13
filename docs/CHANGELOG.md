# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

## [2.1.2] - 2026-07-13

### Changed

- Dev dependency lock sync (`friendsofphp/php-cs-fixer`, `rector/rector`) and demo `composer.lock` files aligned.
- `.gitignore`: ignore local Cursor sandbox file (`.cursor/sandbox.json`).

## [2.1.1] - 2026-07-08

### Fixed

- Test coverage for `QrUrlPolicy` and `InvalidWalletQrUrlException` restored to **100%** (CI coverage gate).

## [2.1.0] - 2026-07-08

### Added

- `QrUrlPolicy` validates URLs in `createQrForUrl` and `wallet_qr_for_url` (http/https only; blocks `javascript:`, `data:`, etc.).
- `InvalidWalletQrUrlException` when a URL is rejected.
- Configuration key `qr_code.url_allowlist` for optional host/URL pattern restrictions.
- GitHub Spec Kit baseline (`specs/001-baseline/`), operator manual (`docs/SPEC-KIT.md`), and Cursor Agent skills.

### Changed

- `WalletQrService` requires `QrUrlPolicy` (injected automatically via Symfony DI).
- Removed legacy `demo/symfony6` (outside supported Symfony 7+ range).
- Demo projects refreshed (Symfony 7.4 / 8.1, debug bundles, dependency updates).
- Documentation: CONFIGURATION, USAGE, SPEC-DRIVEN-DEVELOPMENT, DEMO-FRANKENPHP, CONTRIBUTING.

## [2.0.0] - 2026-06-30

### Changed

- **Breaking:** minimum PHP raised to **8.2**; minimum Symfony raised to **7.0** (`^7.0 || ^8.0`). Symfony 6 and PHP 8.1 are no longer supported.
- CI matrix aligned: PHP 8.2–8.5, Symfony 7.0 / 7.4 / 8.0 / 8.1.
- Documentation updated for new requirements (README, INSTALLATION, CONTRIBUTING, DEMO-FRANKENPHP).

## [1.1.0] - 2026-06-30

### Added

- DI integration tests for `NowoWalletQrExtension`.
- Additional unit tests for `NowoWalletQrBundle` and `WalletQrExtension`.
- Git pre-commit hooks (`.githooks/pre-commit`) installable via `make setup-hooks`.
- CodeRabbit configuration and GitHub workflow.

### Changed

- CI matrix extended with Symfony 7.4 and 8.1.
- Demo projects: Symfony 7 demo targets 7.4; Symfony 8 demos: 8.1.
- Repository URLs corrected to `nowo-tech/WalletQrBundle` in README, `composer.json`, CONTRIBUTING, and Flex post-install message.
- Documentation: Symfony Flex recipe section in INSTALLATION; expanded SPEC-DRIVEN-DEVELOPMENT.

### Fixed

- Duplicate blocks removed from `.github/CODEOWNERS` and `.github/PULL_REQUEST_TEMPLATE.md`.

## [1.0.0] - 2026-06-10

### Added

- Initial release: Google Wallet save links (JWT), Apple Wallet pass URL builder, QR data URI rendering.
- `WalletQrService` with Android/iOS pair generation.
- Twig helpers `wallet_qr_data_uri` and `wallet_qr_for_url`.
- Symfony configuration tree `nowo_wallet_qr`.
- Demo applications for Symfony 6, 7, and 8.
