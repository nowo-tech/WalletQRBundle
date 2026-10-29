# PHP-FIG PSR evaluation (REQ-CS-007)

Package: `nowo-tech/wallet-qr-bundle` (`symfony-bundle`)

This document records which [PHP-FIG PSRs](https://www.php-fig.org/psr/) apply to this package.
Only contracts that add clear interoperability or maintainability value are **Adopted**.
Others are **N/A** (or already covered by Symfony) so the decision stays auditable.

## Table of contents

- [Baseline (always)](#baseline-always)
- [Interface / contract PSRs](#interface--contract-psrs)
- [Summary](#summary)

## Baseline (always)

| PSR | Decision | How |
| --- | -------- | --- |
| PSR-12 (coding style) | **Adopted** | `@PSR12` in `.php-cs-fixer.dist.php` (Nowo REQ-CS-001). |
| PSR-4 (autoloading) | **Adopted** | `composer.json` `autoload` / `autoload-dev` PSR-4 map for package sources and tests. |

## Interface / contract PSRs

Domain inventory: the bundle signs Google Wallet save-link JWTs with a service-account key read from a local file
(`GoogleWalletSaveLinkBuilder`), builds Apple Wallet `.pkpass` download URLs from a pattern
(`AppleWalletPassLinkBuilder`), renders QR codes through `nowo-tech/qr-code-bundle` (`WalletQrService`) and exposes
two Twig functions. No outbound HTTP, no cache, no events, no HTTP controllers.

| PSR | Decision | Notes |
| --- | -------- | ----- |
| PSR-3 Logger | **Adopted** | `GoogleWalletSaveLinkBuilder::__construct(?Psr\Log\LoggerInterface $logger = null)`; wired to `@logger` (null on invalid) with the `monolog.logger` tag, channel `wallet_qr`, in `NowoWalletQrExtension`. Logs signing start/success/failure without secrets ([SECURITY.md](SECURITY.md#logging)). `psr/log` in `require`; tests use an in-memory `AbstractLogger`. |
| PSR-20 Clock | **Adopted** | `GoogleWalletSaveLinkBuilder::__construct(?Psr\Clock\ClockInterface $clock = null)` supplies the JWT `iat` claim; wired to the `Psr\Clock\ClockInterface` alias of FrameworkBundle's `clock` service (null on invalid → `Symfony\Component\Clock\NativeClock`). `psr/clock` + `symfony/clock` in `require`; tests freeze time with `MockClock`. |
| PSR-6 / PSR-16 Cache | **N/A** | No package-owned cache layer (the service-account file is read per signing call, nothing is memoized). |
| PSR-7 / PSR-17 HTTP messages | **N/A** | No HTTP message API: the bundle returns URL strings and data URIs; it has no controllers. |
| PSR-18 HTTP client | **N/A** | No outbound HTTP client: JWTs are signed locally and the browser/device opens `pay.google.com`. |
| PSR-11 Container | **N/A** | No container locator in the public API; constructor injection only. |
| PSR-14 Event dispatcher | **N/A** | No domain events or extension points that need a dispatcher. |
| PSR-15 HTTP middleware | **N/A** | No HTTP middleware pipeline. |

## Summary

- **Adopted beyond baseline:** PSR-3 (Logger) and PSR-20 (Clock), both in `GoogleWalletSaveLinkBuilder`.
- **Rule:** do not add `psr/*` Composer dependencies without matching type-hints and DI wiring.
- **Re-evaluate** when the package gains HTTP, cache, or event SPIs, or logs/time in another service.

---

_REQ-CS-007 evaluation date: 2026-10-29._
