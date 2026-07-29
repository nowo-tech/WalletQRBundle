# Security Policy

## Table of contents

- [Supported Versions](#supported-versions)
- [Reporting a Vulnerability](#reporting-a-vulnerability)
- [Preferred Languages](#preferred-languages)
- [Contact](#contact)
- [Scope and attack surface](#scope-and-attack-surface)
- [Threat model and mitigations](#threat-model-and-mitigations)
- [Dependencies and updates](#dependencies-and-updates)
- [Release security checklist (12.4.1)](#release-security-checklist-1241)

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 2.x     | :white_check_mark: |
| 1.x     | :white_check_mark: |

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
| **Phishing / unsafe QR payloads** | `QrUrlPolicy` (`src/Security/QrUrlPolicy.php`) accepts only `http`/`https` URLs and rejects `javascript:`, `data:`, and other schemes. Optional `qr_code.url_allowlist` restricts hosts/URLs further. |
| **Empty allowlist** | With an empty allowlist, any https URL may be encoded — treat QR content as host-controlled input; do not pass untrusted user URLs without an allowlist. |
| **Google Wallet service-account key** | Path configured via `google_wallet.service_account_json`; keep the JSON file out of git, readable only by the app user, rotate on leak. |
| **JWT origins** | Configure `google_wallet.origins` to your production origins only. |
| **Twig / XSS** | QR helpers emit data-URI images or URLs; escape surrounding HTML as usual. Do not mark untrusted strings `is_safe` without policy checks. |

**REQ-SEC-004:** AI audit + remedia **2026-07-29** — grade **Pass (conditional)** / residual risk **Medium** (phishing if host encodes untrusted URLs without allowlist; SA key permissions on host).

## Dependencies and updates

- Run `composer audit` regularly.
- Keep Symfony, `endroid/qr-code`, and `firebase/php-jwt` updated.
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

Record confirmation in the release PR or tag notes.
