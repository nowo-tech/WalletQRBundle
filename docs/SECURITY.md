# Security Policy

## Table of contents

- [Supported Versions](#supported-versions)
- [Reporting a Vulnerability](#reporting-a-vulnerability)
  - [How to Report](#how-to-report)
  - [Response Timeline](#response-timeline)
  - [Disclosure Policy](#disclosure-policy)
- [Preferred Languages](#preferred-languages)
- [Contact](#contact)
- [Scope and attack surface](#scope-and-attack-surface)
- [Threat model and mitigations](#threat-model-and-mitigations)
- [Secrets and cryptography](#secrets-and-cryptography)
- [Logging](#logging)
- [Dependencies and updates](#dependencies-and-updates)
- [Release security checklist (12.4.1)](#release-security-checklist-1241)

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 3.x     | :white_check_mark: |
| 2.x     | :white_check_mark: |
| 1.x     | :x:                |

## Reporting a Vulnerability

We take the security of Wallet QR Bundle seriously. If you believe you have found a security vulnerability, please report it to us as described below.

### How to Report

**Please do not report security vulnerabilities through public GitHub issues.**

Instead, please send an email to: **hectorfranco@nowo.tech**

Include the following information in your report:

- Type of issue (e.g. phishing via QR payload, JWT misuse, path traversal on key file, etc.)
- Full paths of source file(s) related to the issue
- The location of the affected source code (tag/branch/commit or direct URL)
- Any special configuration required to reproduce the issue
- Step-by-step instructions to reproduce the issue
- Proof-of-concept or exploit code (if possible)
- Impact of the issue, including how an attacker might exploit it

### Response Timeline

- **Initial Response**: Within 48 hours
- **Status Update**: Within 7 days
- **Resolution**: Varies depending on complexity

### Disclosure Policy

- We will confirm receipt of your vulnerability report
- We will work with you to understand and validate the issue
- We will develop and release a fix as quickly as possible
- We will publicly acknowledge your responsible disclosure (if desired)

## Preferred Languages

We prefer all communications to be in English or Spanish.

## Contact

- **Maintainer**: [Héctor Franco Aceituno](https://github.com/HecFranco)
- **Organization**: [nowo-tech](https://github.com/nowo-tech)

## Scope and attack surface

This bundle provides Twig helpers and services to build:

- Google Wallet “Add to Google Wallet” save URLs (signed JWT with a service-account private key)
- Apple Wallet `.pkpass` download URL strings for QR encoding
- PNG QR codes as data URIs for arbitrary http(s) URLs (custom links)

There is **no** bundled HTTP admin UI or public controller. Host applications supply pass data and render Twig output.

## Threat model and mitigations

| Risk | Mitigation |
|------|------------|
| **Phishing / unsafe QR payloads** | `QrUrlPolicy` from required [`nowo-tech/qr-code-bundle`](https://github.com/nowo-tech/QrCodeBundle) accepts only `http`/`https` and rejects `javascript:`, `data:`, etc. Optional `nowo_qr_code.url_allowlist` (or legacy `nowo_wallet_qr.qr_code.url_allowlist`) restricts hosts further. |
| **Allowlist scope** | The allowlist applies to `createQrForUrl()` / `wallet_qr_for_url()` only. Google save links (`pay.google.com`) and Apple pass URLs are built from trusted configuration and are encoded without the allowlist (see [Configuration](CONFIGURATION.md#allowlist-scope)). |
| **Empty allowlist** | With an empty allowlist, any https URL may be encoded — treat QR content as host-controlled input; do not pass untrusted user URLs without an allowlist. |
| **Google Wallet service-account key** | Path configured via `google_wallet.service_account_json`; keep the JSON file out of git, readable only by the app user, rotate on leak. |
| **JWT origins** | Configure `google_wallet.origins` to your production origins only. |
| **Twig / XSS** | QR helpers emit data-URI images or URLs; escape surrounding HTML as usual. Do not mark untrusted strings `is_safe` without policy checks. |

**REQ-SEC-004:** AI audit + remedia **2026-07-29** (Pass (conditional) / Medium); re-audit **2026-09-28** — grade **Pass (good)** / residual risk **Low** (QR URLs through `QrUrlPolicy`; host keeps the Apple / Google keys private and configures the URL allowlist).

## Secrets and cryptography

| Item | How it is handled |
|------|-------------------|
| **What is signed** | Google Wallet save links are JWTs signed with **RS256** (`firebase/php-jwt`) using the service-account `private_key`. Claims: `iss` (service-account email), `aud=google`, `typ=savetowallet`, `iat` (from the injected PSR-20 clock), `payload`, optional `origins`. |
| **Key storage** | The bundle only reads the JSON file at `google_wallet.service_account_json` when a link is built; the key is never cached in a static, written anywhere or exposed through a Twig function. Keep the file outside the web root and outside git, readable only by the PHP user; prefer a path injected from a secret store / `%env()%`. |
| **No hardcoded secrets** | No key, token or password ships in `src/`, the Flex recipe or the demos. The recipe keeps `google_wallet.enabled: false`. `tests/fixtures/google-service-account.json` is a throwaway key generated for the test suite only; it grants access to nothing. |
| **Rotation** | Rotate the service-account key in Google Cloud on any suspected leak and replace the file; previously issued save links stay valid for the passes they reference. |
| **Apple Wallet** | The bundle does **not** sign `.pkpass` files: it only builds the download URL from `pass_download_url_pattern`. Pass signing (Apple certificates) stays in the host application. |

## Logging

`GoogleWalletSaveLinkBuilder` receives the PSR-3 `logger` (Monolog channel `wallet_qr` when MonologBundle is installed; a null logger otherwise):

| Event | Level | Context |
|-------|-------|---------|
| Signing started | `debug` | `bundle`, `action`, `issuer_id`, `origins` (count only) |
| Save link signed | `info` | same keys |
| Configuration error (missing issuer, missing/invalid JSON file, missing `client_email` / `private_key`) | `error` | same keys + `reason` (the bundle's own exception message: may contain the configured file path, never file contents) |
| JWT signing failure | `error` | same keys + `exception_class` only — the underlying message is **not** logged because crypto errors can quote key material |

**Never logged:** the private key, the service-account JSON contents, the signed JWT / save URL, the pass payload (object/class IDs or any personal data in it) and the origin values. Apple Wallet URL building and QR rendering do not log (no I/O, no secrets). The bundle contains no `dump()`, `var_dump()` or `error_log()` calls in `src/`.

## Dependencies and updates

- Run `composer audit` regularly.
- Keep Symfony, `nowo-tech/qr-code-bundle` (and its `endroid/qr-code`), and `firebase/php-jwt` updated.
- Review Dependabot PRs before release (`make check-open-prs`).

## Release security checklist (12.4.1)

Before tagging a release, confirm:

| Item | Notes |
|------|--------|
| **SECURITY.md** | This document is current and linked from the README. |
| **`.gitignore` and `.env`** | `.env` and local env files are ignored; no committed secrets. |
| **No secrets in repo** | No API keys, passwords, tokens, or service-account JSON in tracked files. |
| **Recipe / Flex** | Default recipe or installer templates do not ship production secrets. |
| **Input / output** | QR URLs pass `QrUrlPolicy`; Twig escapes user-controlled HTML context. |
| **Dependencies** | `composer audit` run; issues triaged. |
| **Logging** | Logs do not print private keys, JWTs, or session identifiers unnecessarily. |
| **Cryptography** | Service-account private key from secure config path; never hardcoded. |
| **Permissions / exposure** | No bundled admin routes; host configures any wallet-related controllers. |
| **Limits / DoS** | Host should bound QR size and rate of generation if exposed to end users. |
| **AI security audit (REQ-SEC-004)** | Grade **Pass (good)** / risk **Low** (re-audit 2026-09-28). Recorded in the Nowo monorepo `BUNDLES_SECURITY_ANALYSIS.md`. |

Record confirmation in the release PR or tag notes.
