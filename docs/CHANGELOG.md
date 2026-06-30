# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

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
