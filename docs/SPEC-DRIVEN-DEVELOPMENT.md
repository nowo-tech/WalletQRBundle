# Spec-driven development

## User stories

| ID | Story |
| --- | --- |
| US-01 | **As a** Symfony integrator, **I want** to generate Google Wallet save links **so that** Android users can scan a QR and add a pass. |
| US-02 | **As a** Symfony integrator, **I want** Apple Wallet download URLs **so that** iOS users can scan a QR and install a `.pkpass`. |
| US-03 | **As a** frontend developer, **I want** QR PNG data URIs and Twig helpers **so that** I can render wallet QR codes without extra assets. |
| US-04 | **As a** maintainer, **I want** configuration for issuer credentials and URL patterns **so that** secrets stay out of application code. |
| US-05 | **As a** maintainer, **I want** PHPUnit coverage **so that** JWT signing and URL building regressions are caught in CI. |

## Bundle functional scope

**Goal:** Generate wallet save/download links and QR codes for Google Wallet (Android) and Apple Wallet (iOS).

**In scope**

- Signed Google Wallet JWT save URLs.
- Apple Wallet pass download URL resolution via `{pass_id}` pattern.
- QR code PNG data URI generation.
- Symfony DI configuration and Twig extension.

**Out of scope**

- Creating or signing `.pkpass` archives (use Apple PassKit tooling in the host app).
- Creating Google Wallet classes/objects via REST (host app or separate integration).
- Wallet pass design UI.

## Validating the functional spec

- `make release-check` or `composer qa`
- PHPUnit under `tests/Unit` and `tests/Integration`

## Requirement identifiers (`REQ-*`)

| ID | Where | What it marks |
| --- | --- | --- |
| *(none yet)* | `Makefile`, `demo/**/Makefile` | Add when scripted demo flows need traceability |
