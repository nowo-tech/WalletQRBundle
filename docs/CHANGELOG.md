# Changelog

All notable changes to this project will be documented in this file.

## Table of contents

- [[Unreleased]](#unreleased)
- [[3.2.0] - 2026-10-29](#320---2026-10-29)
- [[3.1.0] - 2026-10-13](#310---2026-10-13)
- [[3.0.12] - 2026-10-13](#3012---2026-10-13)
- [[3.0.11] - 2026-10-09](#3011---2026-10-09)
- [[3.0.10] - 2026-09-27](#3010---2026-09-27)
- [[3.0.9] - 2026-09-25](#309---2026-09-25)
- [[3.0.8] - 2026-09-07](#308---2026-09-07)
- [[3.0.7] - 2026-08-24](#307---2026-08-24)
- [[3.0.6] - 2026-08-20](#306---2026-08-20)
- [[3.0.5] - 2026-08-19](#305---2026-08-19)
- [[3.0.4] - 2026-08-19](#304---2026-08-19)
- [[3.0.3] - 2026-08-19](#303---2026-08-19)
- [[3.0.2] - 2026-08-18](#302---2026-08-18)
- [[3.0.1] - 2026-07-30](#301---2026-07-30)
- [[3.0.0] - 2026-07-30](#300---2026-07-30)
- [[2.1.5] - 2026-07-29](#215---2026-07-29)
- [[2.1.4] - 2026-07-22](#214---2026-07-22)
- [[2.1.3] - 2026-07-16](#213---2026-07-16)
- [[2.1.2] - 2026-07-13](#212---2026-07-13)
- [[2.1.1] - 2026-07-08](#211---2026-07-08)
- [[2.1.0] - 2026-07-08](#210---2026-07-08)
- [[2.0.0] - 2026-06-30](#200---2026-06-30)
- [[1.1.0] - 2026-06-30](#110---2026-06-30)
- [[1.0.0] - 2026-06-10](#100---2026-06-10)

## [Unreleased]

## [3.2.0] - 2026-10-29

WalletQr: injected clock + PSR-3 logging around Google Wallet signing, strict qr_code enum (legacy node deprecated) See [UPGRADING](UPGRADING.md#to-320).

### Added

- `GoogleWalletSaveLinkBuilder` takes an optional PSR-20 clock (JWT `iat`, defaults to FrameworkBundle's `clock`) and an optional PSR-3 logger (`wallet_qr` Monolog channel). Signing start/success/failure is logged without secrets (REQ-DI-001, REQ-OBS-001, REQ-CS-007). New runtime requirements: `symfony/clock`, `psr/clock`, `psr/log`.
- `docs/SECURITY.md`: "Secrets and cryptography" and "Logging" sections (REQ-SEC-001).

### Changed

- `nowo_wallet_qr.qr_code.error_correction` is an enum node (`low`, `medium`, `quartile`, `high`); invalid values fail compilation with the path and the allowed values (REQ-SF-006).
- Flex recipe and demos no longer set the legacy `qr_code` block; QR options live in `nowo_qr_code`.
- `composer.json` `homepage` / `support` and the CodeRabbit comments use the GitHub slug `nowo-tech/WalletQRBundle` (REQ-DOCS-014).
- `.github/SECURITY.md` lists the supported 3.x / 2.x lines (REQ-SEC-003); `docs/PSR.md` re-evaluated (PSR-3 and PSR-20 adopted, REQ-CS-007).
- PHPDoc on every public class and method in `src/` (REQ-CS-001); tests use PHPUnit attributes (REQ-TEST-010); README "Tests and coverage" lists exact percentages (REQ-TEST-007); Makefile `help` follows the standard block order (REQ-MAKE-001).
- Cursor rules: dropped the frontend/Vite rule (the bundle ships no frontend assets) (REQ-IDE-004).
- Developer tooling: Rector targets PHP 8.2, the declared `php` floor (REQ-CS-003, REQ-SF-001). Rector's PHP 8.2 rules made the final services / value objects `readonly` classes (no API change).
- Dependencies refreshed (Composer and pnpm/npm lock files of the bundle and its demos, campaign 2026-10-19).
- `.php-cs-fixer.dist.php`: `new ParallelConfig(2)` (REQ-CS-001).

### Deprecated

- `nowo_wallet_qr.qr_code` (and its keys): configure `nowo_qr_code` instead. Setting it triggers a Symfony config deprecation; values are still forwarded (REQ-SF-006, REQ-BC-001).

### Documentation

- Documentation: wave-1 REQ compliance — README preamble order and canonical FrankenPHP claim, GitHub links use the `WalletQRBundle` repository slug, documentation list order (PSR under Additional documentation), canonical issue templates, CONTRIBUTING Code of Conduct link and Cursor Agent queue settings (REQ-IDE-006), demo README rewritten for the two current demos with a DEMO-FRANKENPHP link, commented demo `.env.example`, Spec Kit baseline acceptance criteria / non-goals / assumptions, tables of contents on long docs.

[3.2.0]: https://github.com/nowo-tech/WalletQRBundle/releases/tag/v3.2.0

## [3.1.0] - 2026-10-13

### Fixed

- `google_wallet.enabled: true` without `issuer_id` / `service_account_json`, or `apple_wallet.enabled: true` with an empty pattern or one without `{pass_id}`, now fails container compilation with `InvalidConfigurationException` instead of silently disabling the integration (`%env()%` patterns are still validated at runtime).
- Twig `wallet_qr_for_url()` with an unknown platform throws a `RuntimeError` listing the accepted values (`android`, `ios`) instead of a bare `ValueError` from `WalletPlatform::from()`.

### Changed

- Minimum Symfony version raised from 7.0 to **7.4**; the CI `7.0` cell (which resolved to 7.4 anyway) is dropped.
- `symfony/yaml` moved from `require-dev` to `require` (the extension loads `services.yaml` with `YamlFileLoader`).
- Documentation: the QR URL allowlist applies only to `createQrForUrl()` / `wallet_qr_for_url()`; Google/Apple wallet links built from configuration are not checked against it (docs/CONFIGURATION.md#allowlist-scope).

[3.1.0]: https://github.com/nowo-tech/WalletQRBundle/releases/tag/v3.1.0

## [3.0.12] - 2026-10-13

### Changed

- Development: `composer.json` pins `config.platform.php` to 8.2.0 so the committed lock stays installable on the minimum PHP; CI overrides the platform per matrix cell.

[3.0.12]: https://github.com/nowo-tech/WalletQRBundle/releases/tag/v3.0.12

## [3.0.11] - 2026-10-09

### Dependencies

- Dependabot: `firebase/php-jwt` 7.2.1, `igor-php/igor-php` `^0.10.0` (dev), `phpstan/phpstan-phpunit`.
- Composer refresh: `nowo-tech/qr-code-bundle` 1.5.0, `nowo-tech/form-kit-bundle` 2.6.1, `nowo-tech/ui-kit-bundle` 1.9.2 (lockfile); dev tooling `phpstan/phpstan` 2.3.1, `rector/rector` 2.7.0, `nowo-tech/phpstan-frankenphp` 1.2.3.
- Demos: Symfony 8.1.8, `firebase/php-jwt` 7.2.1, `twig/twig` 3.30.0; regenerated `config/reference.php`.
- PHP CS Fixer bot passes (no functional change).

[3.0.11]: https://github.com/nowo-tech/WalletQRBundle/releases/tag/v3.0.11

## [3.0.10] - 2026-09-27

### Added

- **REQ-CS-008:** `igor-php/igor-php` (require-dev only), root `igor.json`, Composer/`Makefile` `igor` target, and `release-check` wiring for FrankenPHP worker-state audit.

### Changed

- **Worker safety (Igor):** justified `// @igor-ignore` annotations and/or `ResetInterface` / request-scoped fixes so `make igor` passes on package `src/`.

[3.0.10]: https://github.com/nowo-tech/WalletQRBundle/releases/tag/v3.0.10

## [3.0.9] - 2026-09-25

### Changed

- **Dependency:** require `nowo-tech/qr-code-bundle` **`^1.4.9`** (FrankenPHP worker + `reset_kernel` false / no `services_resetter` safe for YAML and database QR profiles).
- **Docs:** FrankenPHP worker audit ([`FRANKENPHP-WORKER-AUDIT.md`](FRANKENPHP-WORKER-AUDIT.md)), DEMO-FRANKENPHP / README / SPEC pointers; baseline `FR-WRK-001`.
- PHPStan (level 8) also analyses the BC `class_alias` files.
- **Demos:** `make -C demo release-check` runs functional tests (not coverage) so FrankenPHP images without pcov do not fail the gate.

### Notes

- **No Wallet QR API changes.** Update both packages: `composer update nowo-tech/wallet-qr-bundle nowo-tech/qr-code-bundle`.

[3.0.9]: https://github.com/nowo-tech/WalletQRBundle/releases/tag/v3.0.9

## [3.0.8] - 2026-09-07

### Changed

- **Dependency:** bump `nowo-tech/qr-code-bundle` from **1.4.7** to **1.4.8**.
- **Deps (dev):** phpstan / rector / php-cs-fixer bumps and Composer lock refresh.

### Notes

- **No API or configuration changes** for Wallet QR itself. Pull the updated QrCodeBundle with Composer.

[3.0.8]: https://github.com/nowo-tech/WalletQRBundle/releases/tag/v3.0.8

## [3.0.7] - 2026-08-24

### Changed

- **Docs:** PHP-FIG PSR evaluation (REQ-CS-007).
- **Style:** PHP CS Fixer alignment.

### Notes

- **No API or configuration changes** for integrators unless noted above.

[3.0.7]: https://github.com/nowo-tech/WalletQRBundle/releases/tag/v3.0.7

## [3.0.6] - 2026-08-20

### Security

- **Flex recipe `when@prod`:** stop shipping `qr_code.url_allowlist: [example.com]`. Production must set real hosts (or rely on QrCodeBundle `^1.4.6` fail-fast when `url_allowlist_required` is true and the allowlist is empty/placeholder).

[3.0.6]: https://github.com/nowo-tech/WalletQRBundle/releases/tag/v3.0.6

## [3.0.5] - 2026-08-19

### Changed

- **Maintainer:** refresh `composer.lock` after PHP CS Fixer CI run.

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
