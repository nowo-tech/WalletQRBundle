# Code inventory — 100% traceability

**Baseline spec**: [`spec.md`](spec.md)  
**Package**: `nowo-tech/wallet-qr-bundle`  
**Last audited**: 2026-07-07

This file proves that **every production source artifact** under `src/` is referenced by the baseline specification. Test-only files under `tests/` and `*.test.ts` under `src/` are out of Packagist scope. Built assets under `Resources/public/` are documented as Vite/build outputs.

## Bundle & DI

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `DependencyInjection/Configuration.php` | Config tree | FR-CFG-001 |
| `DependencyInjection/NowoWalletQrExtension.php` | DI extension | FR-CFG-002 |
| `NowoWalletQrBundle.php` | Bundle entry | FR-BUNDLE-001 |

## Domain models

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `Enum/WalletPlatform.php` | Domain enum | FR-MDL-001 |
| `Model/GoogleWalletPassReference.php` | Domain model | FR-MDL-002 |
| `Model/WalletLink.php` | Domain model | FR-MDL-002 |
| `Model/WalletQr.php` | Domain model | FR-MDL-002 |

## Security

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `Security/QrUrlPolicy.php` | URL/HTML policy | FR-SEC-004 |

## Twig PHP

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `Twig/WalletQrExtension.php` | Twig extension | FR-TWIG-001 |

## Exceptions

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `Exception/InvalidWalletQrUrlException.php` | Domain exception | FR-ERR-001 |
| `Exception/WalletConfigurationException.php` | Domain exception | FR-ERR-001 |

## Wallet & QR

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `AppleWallet/AppleWalletPassLinkBuilder.php` | Wallet link builder | FR-WALLET-001 |
| `GoogleWallet/GoogleWalletSaveLinkBuilder.php` | Wallet link builder | FR-WALLET-001 |
| `QrCode/QrCodeDataUriRenderer.php` | QR data-URI renderer | FR-QR-001 |
| `Service/WalletQrService.php` | Wallet QR orchestration | FR-WALLET-002 |

## Symfony config

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `Resources/config/services.yaml` | Service wiring | FR-DI-001 |

## Coverage summary

| Category | Files | Mapped |
| --- | ---: | ---: |
| Bundle & DI | 3 | 3 |
| Domain models | 4 | 4 |
| Security | 1 | 1 |
| Twig PHP | 1 | 1 |
| Exceptions | 2 | 2 |
| Wallet & QR | 4 | 4 |
| Symfony config | 1 | 1 |
| **Total production sources** | **16** | **16** |

Audit: `find src -type f ! -path '*/assets/dist/*' ! -name '*.test.ts' | wc -l`
