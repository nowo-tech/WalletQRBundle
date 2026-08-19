# Changelog

All notable changes to this project will be documented in this file.

## Table of contents

- [[Unreleased]](#unreleased)
- [[3.0.4] - 2026-08-19](#304---2026-08-19)
- [[3.0.3] - 2026-08-19](#303---2026-08-19)
- [[3.0.2] - 2026-08-18](#302-2026-08-18)
- [[3.0.1] - 2026-07-30](#301-2026-07-30)
- [[3.0.0] - 2026-07-30](#300-2026-07-30)
- [[2.1.5] - 2026-07-29](#215-2026-07-29)
- [[2.1.4] - 2026-07-22](#214-2026-07-22)
- [[2.1.3] - 2026-07-16](#213-2026-07-16)
- [[2.1.2] - 2026-07-13](#212-2026-07-13)
- [[2.1.1] - 2026-07-08](#211-2026-07-08)
- [[2.1.0] - 2026-07-08](#210-2026-07-08)
- [[2.0.0] - 2026-06-30](#200-2026-06-30)
- [[1.1.0] - 2026-06-30](#110-2026-06-30)
- [[1.0.0] - 2026-06-10](#100-2026-06-10)

## [Unreleased]

## [3.0.4] - 2026-08-19

### Documentation

- Sync `CHANGELOG.md` and `UPGRADING.md` for v3.0.3 release notes.

## [3.0.3] - 2026-08-19

### Security

- **Flex recipe `when@prod`:** `qr.url_allowlist` placeholder for SSRF-safe wallet QR generation in production.
- **CI:** run `composer audit --locked` after dependency install (REQ-SEC / P3).

## [3.0.2] - 2026-08-18

### Changed

- **Demos:** pin `nowo-tech/hot-reload-bundle` to `^1.4` with FrankenPHP Mercure/`hot_reload` (`dev`/`test` only).
- **Demos:** Symfony 8 only; Symfony 6/7 demo apps removed.

## [3.0.1] - 2026-07-30

### Added

- Unit test: `NowoWalletQrExtension::prepend` skips configs without a `qr_code` array (coverage for BC bridge onto `nowo_qr_code`).

### Documentation

- **[USAGE](USAGE.md):** Twig helpers only — no HTML template override surface (REQ-TWIG-001 N/A); pointer to QrCodeBundle for QR widgets.
- **[UPGRADING](UPGRADING.md)** section **3.0.1**.

## [3.0.0] - 2026-07-30

### Added

- Required Composer dependency on [`nowo-tech/qr-code-bundle`](https://packagist.org/packages/nowo-tech/qr-code-bundle) (^1.1).
- Flex recipe registers `NowoQrCodeBundle` together with `NowoWalletQrBundle`.
- `NowoWalletQrBundle::build()` fails fast if `NowoQrCodeBundle` is not enabled.
- Legacy `nowo_wallet_qr.qr_code` config is prepended onto `nowo_qr_code` for backward compatibility.

### Changed

- **Breaking:** QR PNG rendering lives in QrCodeBundle. `WalletQrService` depends on `Nowo\QrCodeBundle\Service\QrCodeService`.
- Prefer configuring QR options under `nowo_qr_code` (profiles / flat keys normalized by QrCodeBundle).
- Demos register `NowoQrCodeBundle` and resolve `qr-code-bundle` from Packagist.

### Deprecated

- BC class aliases (same underlying QrCodeBundle classes):
  - `Nowo\WalletQrBundle\QrCode\QrCodeDataUriRenderer`
  - `Nowo\WalletQrBundle\Security\QrUrlPolicy`
  - `Nowo\WalletQrBundle\Exception\InvalidWalletQrUrlException` → use `Nowo\QrCodeBundle\Exception\InvalidQrUrlException`

### Removed

- Direct Composer requirement on `endroid/qr-code` (now transitive via QrCodeBundle).

## [2.1.5] - 2026-07-29

### Added

- FrankenPHP Friendly Worker Mode banner (REQ-DOCS-017) after PHPStan FrankenPHP rules (REQ-CS-005).
- `make check-open-prs` / `.scripts/check-open-prs.sh` (REQ-REL-003); `make demo-smoke` (REQ-TEST-011).
- `SYMFONY_DEPRECATIONS_HELPER=max[direct]=0` in PHPUnit and CI (REQ-SF-005).
- Threat model for `QrUrlPolicy` in `docs/SECURITY.md` (REQ-SEC-004).

### Fixed

- Demo Docker images install PHP **gd** (required by `endroid/qr-code` PNG rendering).
- Flat DI parameters for `qr_code.*` so Symfony can inject `%nowo_wallet_qr.config.qr_code.size%` (nested array parameters are not resolvable).

### Changed

- Symfony 8 demos use `frankenphp:1-php8.5-alpine` (REQ-DEMO-010).
- README Documentation order (REQ-DOCS-002); Symfony badge includes **7.4** floor (REQ-DOCS-004).
- Packagist keywords include `php` and `frankenphp` (REQ-PKG-004).
- Bundle / DI classes marked `final` (REQ-PHP-001).
- `phpstan.neon.dist`: explicit `ignoreErrors: []` (REQ-CS-006).

## [2.1.4] - 2026-07-22

### Changed

- Demo FrankenPHP entrypoints use **`FRANKENPHP_MODE`** (`classic` \| `worker`, default `worker`) instead of switching Caddyfile from `APP_ENV` alone (REQ-DEMO-010).
- Shared `docker/entrypoint.sh` in Symfony 7 / 8 / 8-php85 demos; documented in [DEMO-FRANKENPHP.md](DEMO-FRANKENPHP.md).
- PHP CS Fixer: `fully_qualified_strict_types` with `import_symbols: true`.

## [2.1.3] - 2026-07-16

### Added

- CI job and local tooling for **REQ-GIT-001** (no Cursor co-author trailers): `.scripts/check-no-cursor-coauthor.sh`, `.githooks/commit-msg`, `make check-no-cursor-coauthor` / `make strip-cursor-coauthor-from-history`.
- [Code of Conduct](../CODE_OF_CONDUCT.md) and [GitHub Actions CI requirements](GITHUB_CI.md).
- Cursor rule `.cursor/rules/01-git-commits.mdc`.

### Changed

- `make release-check` and `make setup-hooks` include git-hygiene checks.
- CONTRIBUTING and RELEASE docs updated for REQ-GIT-001.
- Dev and demo `composer.lock` files synced.

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
