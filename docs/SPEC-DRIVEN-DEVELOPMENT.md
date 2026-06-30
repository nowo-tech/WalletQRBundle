# Spec-driven development

In this repository, **spec-driven development** has two layers:

1. **Product behavior** — what **WalletQrBundle** guarantees (see [`USAGE.md`](USAGE.md), [`CONFIGURATION.md`](CONFIGURATION.md)).
2. **Traceability anchors** — stable **`REQ-*`** identifiers in Makefiles and demos when scripted flows need discoverability.

Tests and static analysis are the mechanical proof alongside this document.

---

## User stories

| ID | Story |
| --- | --- |
| US-01 | **As a** Symfony integrator, **I want** Google Wallet save links **so that** Android users can scan a QR and add a pass. |
| US-02 | **As a** Symfony integrator, **I want** Apple Wallet download URLs **so that** iOS users can scan a QR and install a `.pkpass`. |
| US-03 | **As a** frontend developer, **I want** QR PNG data URIs and Twig helpers **so that** I can render wallet QR codes without extra assets. |
| US-04 | **As a** maintainer, **I want** configuration for issuer credentials and URL patterns **so that** secrets stay out of application code. |
| US-05 | **As a** maintainer, **I want** PHPUnit coverage **so that** JWT signing and URL building regressions are caught in CI. |

---

## Bundle functional scope

**Goal:** Generate wallet save/download links and QR codes for Google Wallet (Android) and Apple Wallet (iOS).

**In scope:** Signed Google Wallet JWT URLs, Apple pass download URL builder, QR data URIs, Symfony DI, Twig extension.

**Out of scope:** Creating `.pkpass` archives, Google Wallet REST object creation, wallet pass design UI.

---

## Validating the functional spec

- Run **`make release-check`** or **`composer qa`** (see [`CONTRIBUTING.md`](CONTRIBUTING.md)).
- PHPUnit under `tests/Unit`; CI enforces **100%** PHP line coverage.

---

## Requirement identifiers (`REQ-*`)

| ID | Where | What it marks |
| --- | --- | --- |
| REQ-MAKE-008 | Root and demo `Makefile` | Shared `update-deps` targets (monorepo `.scripts`) |

---

## Suggested workflow for contributors

1. Clarify behavior in an issue or draft PR.
2. Implement with tests and static analysis.
3. Update integrator docs when configuration or public API changes.

---

## Relationship to Engram

[`ENGRAM.md`](ENGRAM.md) covers Nowo-wide checklist items. This document ties **product behavior**, **verification**, and local **`REQ-*`** habits.

---

## See also

- [`USAGE.md`](USAGE.md)
- [`CONFIGURATION.md`](CONFIGURATION.md)
- [`DEMO-FRANKENPHP.md`](DEMO-FRANKENPHP.md)
