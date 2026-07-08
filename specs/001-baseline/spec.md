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

**Acceptance**: `GoogleWalletSaveLinkBuilder` signs JWT; `WalletQrService` composes `WalletQr`.

### US-02 — Apple Wallet download link + QR (Priority: P1)

As an integrator, I build Apple pass download URLs from configured base URL and pass id.

**Acceptance**: `AppleWalletPassLinkBuilder` validates configuration.

### US-03 — Twig helper (Priority: P2)

As a template author, I render wallet QR markup via `WalletQrExtension`.

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

---

## Success Criteria

- **SC-001**: 100% of production files in `src/` appear in [`code-inventory.md`](code-inventory.md) with requirement IDs (16/16 mapped).
- **SC-002**: Configuration keys in `docs/CONFIGURATION.md` match `Configuration.php`.
- **SC-003**: `composer qa` / `make release-check` pass in CI (PHPUnit, PHPStan, Vitest where applicable).
- **SC-004**: No Packagist-visible behavior change without spec, inventory, and test updates.

---

## Validation

| Check | Command |
| --- | --- |
| Full QA | `make release-check` or `composer qa` |
| Code inventory audit | `find src -type f ! -path '*/assets/dist/*' ! -name '*.test.ts' \| wc -l` |
| TS tests | `pnpm test` or `make test-ts` (when assets present) |

When changing behavior, update this spec, `code-inventory.md`, integrator docs, and tests.
