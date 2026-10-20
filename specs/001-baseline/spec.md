# Feature Specification: WalletQrBundle baseline (100% code coverage)

**Feature Branch**: `001-baseline`  
**Created**: 2026-07-07  
**Status**: Active  
**Input**: Backfill GitHub Spec Kit baseline documenting 100% of production code in `src/`.

**Related docs**: [`docs/SPEC-DRIVEN-DEVELOPMENT.md`](../../docs/SPEC-DRIVEN-DEVELOPMENT.md), [`docs/CONFIGURATION.md`](../../docs/CONFIGURATION.md), [`docs/USAGE.md`](../../docs/USAGE.md)  
**Code inventory (traceability)**: [`code-inventory.md`](code-inventory.md)

---

## Summary

**Package**: `nowo-tech/wallet-qr-bundle`  
**Configuration root**: `nowo_wallet_qr`


Library-style Symfony bundle to build **Google Wallet save links** (signed JWT) and **Apple Wallet download URLs**, rendered as **QR code data URIs** for mobile pass onboarding.

---

## User Scenarios & Testing

### US-01 — Google Wallet save link + QR (Priority: P1)

As an integrator, I pass a `GoogleWalletPassReference` and receive a signed save URL and QR PNG data URI.

**Acceptance**:

1. **Given** configured Google issuer credentials and a `GoogleWalletPassReference`, **When** `WalletQrService` builds the Google link, **Then** `GoogleWalletSaveLinkBuilder` returns a `https://pay.google.com/gp/v/save/<signed JWT>` URL wrapped in a `WalletLink`.
2. **Given** that link, **When** the QR is requested, **Then** a `WalletQr` with a PNG data URI is returned (rendered by `nowo-tech/qr-code-bundle`).

### US-02 — Apple Wallet download link + QR (Priority: P1)

As an integrator, I build Apple pass download URLs from configured base URL and pass id.

**Acceptance**:

1. **Given** a configured Apple pass base URL, **When** a pass id is passed, **Then** `AppleWalletPassLinkBuilder` returns the download URL.
2. **Given** a missing base URL, **When** an Apple link is requested, **Then** `WalletConfigurationException` is thrown.

### US-03 — Twig helper (Priority: P2)

As a template author, I render wallet QR markup via `WalletQrExtension`.

**Acceptance**:

1. **Given** a platform value (`android` / `ios`) and a wallet URL, **When** the template calls `wallet_qr_for_url(platform, url)`, **Then** a `WalletQr` is returned and `wallet_qr_data_uri(walletQr)` yields the PNG data URI for `<img src>`.
2. **Given** an unknown platform value, **When** `wallet_qr_for_url()` is called, **Then** a Twig `RuntimeError` lists the accepted platforms.

---

## Requirements

- **FR-BUNDLE-001 / FR-CFG-001 / FR-CFG-002**: Bundle registration and issuer/credential configuration.
- **FR-WALLET-001 / FR-WALLET-002**: Platform link builders and orchestrating `WalletQrService`.
- **FR-QR-001**: `QrCodeDataUriRenderer` produces inline PNG data URIs.
- **FR-MDL-001 / FR-MDL-002**: `WalletPlatform`, `WalletLink`, `WalletQr`, `GoogleWalletPassReference`.
- **FR-SEC-004**: `QrUrlPolicy` restricts allowed URL schemes for QR payloads.
- **FR-TWIG-001**: Twig functions/filters for integrator templates.
- **FR-ERR-001**: Configuration and invalid URL exceptions.
- **FR-DI-001**: Autowired services YAML.
- **FR-WRK-001**: FrankenPHP worker mode with kernel **not** reset between requests (`reset_kernel` / equivalent off) — all WalletQrBundle services are stateless; QR rendering relies on `nowo-tech/qr-code-bundle` `^1.4.9` (also scenario-B safe). See [`docs/FRANKENPHP-WORKER-AUDIT.md`](../../docs/FRANKENPHP-WORKER-AUDIT.md).

---

## Success Criteria

- **SC-001**: 100% of production files in `src/` appear in [`code-inventory.md`](code-inventory.md) with requirement IDs (16/16 mapped, audited 2026-10-20).
- **SC-002**: Configuration keys in `docs/CONFIGURATION.md` match `Configuration.php`.
- **SC-003**: `composer qa` / `make release-check` pass in CI (PHP-CS-Fixer, PHPStan, PHPUnit). The package ships no TypeScript.
- **SC-004**: No Packagist-visible behavior change without spec, inventory, and test updates.

---

## Non-goals

- Issuing or signing Apple `.pkpass` files: the bundle builds the download URL; the host serves the signed pass.
- Creating Google Wallet classes/objects through the Google Wallet REST API: the bundle signs save-link JWTs for passes the issuer already defines.
- QR rendering itself (delegated to `nowo-tech/qr-code-bundle`).
- `demo/` trees (illustrative, not Packagist API).

## Assumptions

- The host supplies Google issuer / service-account credentials and the Apple pass base URL through `nowo_wallet_qr` configuration (secrets via env vars).
- QR payloads are wallet URLs validated by the QrCodeBundle URL policy (`QrUrlPolicy` is a deprecated alias of it since 3.0).

---

## Validation

| Check | Command |
| --- | --- |
| Full QA | `make release-check` or `composer qa` |
| Code inventory audit | `git ls-files src \| wc -l` → 16 |

When changing behavior, update this spec, `code-inventory.md`, integrator docs, and tests.
