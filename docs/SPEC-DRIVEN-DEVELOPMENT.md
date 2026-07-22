# Spec-driven development

In this repository, **spec-driven development** has three layers that stay in sync:

1. **GitHub Spec Kit baseline** — [`specs/001-baseline/`](../specs/001-baseline/) ([`spec.md`](../specs/001-baseline/spec.md), [`code-inventory.md`](../specs/001-baseline/code-inventory.md)), initialized with [GitHub Spec Kit](https://github.com/github/spec-kit) (`.specify/`, **Cursor Agent** skills in `.cursor/skills/speckit-*`). The inventory maps **100%** of production code in `src/`. **How to install, initialize, and use Spec Kit:** [`SPEC-KIT.md`](SPEC-KIT.md).
2. **Product behavior** — what **WalletQrBundle** guarantees (see [`USAGE.md`](USAGE.md), [`CONFIGURATION.md`](CONFIGURATION.md)). **PHPUnit** and **PHPStan** enforce contracts in CI where applicable.
3. **Traceability anchors** — stable **`REQ-*`** identifiers in Makefiles and demos when scripted flows need discoverability.

There is no separate executable spec language (for example Gherkin); Spec Kit specs, tests, and static analysis are the mechanical proof alongside this document.

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
| REQ-GIT-001 | CI, `.scripts/`, `.githooks/commit-msg` | No Cursor co-author trailers in git history |
| REQ-DEMO-010 | Demo `docker/entrypoint.sh`, `.env.example` | `FRANKENPHP_MODE` classic \| worker |

---

## Suggested workflow for contributors

1. Clarify behavior in an issue or draft PR.
2. Implement with tests and static analysis.
3. Update integrator docs when configuration or public API changes.
4. **Ship integrator docs** when behavior or configuration changes: [`USAGE.md`](USAGE.md), [`CONFIGURATION.md`](CONFIGURATION.md), [`CHANGELOG.md`](CHANGELOG.md), and [`UPGRADING.md`](UPGRADING.md) when consumers must change code or config.
5. **Keep Spec Kit artifacts in sync** when production code under `src/` changes:
   - Update [`specs/001-baseline/spec.md`](../specs/001-baseline/spec.md) and [`code-inventory.md`](../specs/001-baseline/code-inventory.md).
   - Follow the maintainer checklist in [`SPEC-KIT.md`](SPEC-KIT.md).
   - For **new features**, use Cursor Agent skills (`/speckit-specify`, `/speckit-plan`, `/speckit-tasks`) as documented in SPEC-KIT.

---


## GitHub Spec Kit (summary)

This repository uses [GitHub Spec Kit](https://github.com/github/spec-kit) with **Cursor Agent** (`cursor-agent` integration).

| Artifact | Path |
| --- | --- |
| **Operator manual** (install, init, usage) | [`SPEC-KIT.md`](SPEC-KIT.md) |
| Baseline spec | [`specs/001-baseline/spec.md`](../specs/001-baseline/spec.md) |
| Code inventory (100%) | [`specs/001-baseline/code-inventory.md`](../specs/001-baseline/code-inventory.md) |
| Constitution | [`.specify/memory/constitution.md`](../.specify/memory/constitution.md) |
| Cursor Agent skills | [`.cursor/skills/`](../.cursor/skills/) (`speckit-*`) |

**Quick start (maintainers):**

```bash
# Install Specify CLI (once per machine) — see SPEC-KIT.md
specify init --here --force --integration cursor-agent --script sh
specify integration list   # Cursor → installed (default)
```

In Cursor Agent, start a new feature with `/speckit-specify <description>`. For day-to-day tooling details, skills reference, folder layout, and troubleshooting, read **[`SPEC-KIT.md`](SPEC-KIT.md)**.

---

## Relationship to Engram

[`ENGRAM.md`](ENGRAM.md) covers Nowo-wide checklist items. This document ties **product behavior**, **verification**, and local **`REQ-*`** habits.

---

## See also

- [`SPEC-KIT.md`](SPEC-KIT.md) — GitHub Spec Kit manual (install, structure, usage)
- [`USAGE.md`](USAGE.md)
- [`CONFIGURATION.md`](CONFIGURATION.md)
- [`DEMO-FRANKENPHP.md`](DEMO-FRANKENPHP.md)
