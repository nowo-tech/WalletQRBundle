# FrankenPHP worker mode audit (kernel not reset between requests)

| Field | Value |
|-------|-------|
| Package | `nowo-tech/wallet-qr-bundle` (`symfony-bundle`) |
| Audited revision | `v3.0.9` (post QrCodeBundle `^1.4.9` pin) |
| Audit date | 2026-09-25 |
| Method | Manual review of every file under `src/` (services, link builders, Twig extension, DI extension, config, `class_alias` BC shims) |
| **Verdict** | ✅ **Compatible under scenario B** — every service of this bundle is stateless (`readonly` config only). QR rendering is delegated to [`nowo-tech/qr-code-bundle`](https://github.com/nowo-tech/QrCodeBundle) **`^1.4.9`**, which is likewise compatible under scenario B (YAML and database profile modes). |

## Execution model assumed

FrankenPHP worker mode boots the Symfony kernel once per worker and serves many requests with the same container. This audit assumes the **strict** variant: the kernel is **not** rebooted between requests (`reset_kernel` / equivalent **off**), so every shared service, static property and PHP global survives from one request to the next. Two scenarios are evaluated:

- **A — kernel not rebooted, `services_resetter` still runs:** services tagged `kernel.reset` (or implementing `ResetInterface`) are reset between requests.
- **B — no reset at all:** nothing is reset; any per-request state kept in a service leaks into the next request.

A bundle that is safe under **B** is safe under **A** and under classic mode / PHP-FPM.

## Summary

| Area | Status | Notes |
|------|--------|-------|
| Mutable state in shared services | ✅ | `WalletQrService`, `GoogleWalletSaveLinkBuilder`, `AppleWalletPassLinkBuilder` and `WalletQrExtension` only have `private readonly` constructor properties |
| Static properties / `static` locals | ✅ | None. Only pure static helpers (`isConfigured()`, `GoogleWalletPassReference::withIssuer()`) |
| `ResetInterface` / `kernel.reset` coverage | ✅ N/A | Nothing to reset on this bundle's services |
| Request / user / locale captured in services | ✅ | Everything is passed as method arguments; the JWT `iat` is computed per call (`GoogleWalletSaveLinkBuilder::buildSaveLinkFromPayload`) |
| Superglobals, `$_ENV`, `putenv`, `ini_set`, `setlocale`, timezone | ✅ | None used; config is compiled into container parameters / definitions |
| Doctrine / EntityManager | ✅ N/A | No persistence in this bundle; see W-02 for the QrCodeBundle dependency |
| Output, headers, `exit`, shutdown functions | ✅ | None |
| Resources (files, sockets, cURL) held open | ✅ | Only a one-shot `file_get_contents()` of the service account JSON (locals only) |
| Memory growth across requests | ✅ | No caches or accumulating arrays |
| Blocking I/O and timeouts | ✅ | No network I/O; the Google save link is signed locally (no HTTP call to Google) |
| Third-party static state | ✅ | `Firebase\JWT\JWT::encode()` only reads the static `$supported_algs` map; it does not touch `JWT::$timestamp` / `$leeway` |
| PHPStan FrankenPHP rulesets | ✅ | `ruleset-classic.neon` + `ruleset-worker.neon` included in `phpstan.neon.dist` |

Worker demo: `demo/symfony8/docker/frankenphp/Caddyfile` and `demo/symfony8-php85/docker/frankenphp/Caddyfile` use `worker { file /app/public/index.php; watch }` (no kernel reboot between requests).

## Services reviewed

| Service | Shared | Mutable state | Scenario A | Scenario B |
|---------|--------|---------------|------------|------------|
| `Nowo\WalletQrBundle\Service\WalletQrService` | yes | none (`readonly` dependencies) | ✅ | ✅ |
| `nowo_wallet_qr.google_wallet.save_link_builder` (`GoogleWalletSaveLinkBuilder`, only when enabled) | yes | none (`readonly` issuer id, JSON path, origins) | ✅ | ✅ |
| `nowo_wallet_qr.apple_wallet.pass_link_builder` (`AppleWalletPassLinkBuilder`, only when enabled) | yes | none (`readonly` URL pattern) | ✅ | ✅ |
| `Nowo\WalletQrBundle\Twig\WalletQrExtension` | yes | none | ✅ | ✅ |

`WalletLink`, `WalletQr` and `GoogleWalletPassReference` are `final readonly` value objects created per call and never stored in a service. `src/QrCode/QrCodeDataUriRenderer.php`, `src/Security/QrUrlPolicy.php` and `src/Exception/InvalidWalletQrUrlException.php` are only `class_alias()` shims to QrCodeBundle classes (executed once when autoloaded). The `Nowo\WalletQrBundle\` resource in `src/Resources/config/services.yaml` also registers the model classes as private services, but they are unused and removed by the container.

## Findings

### W-01 — Service account JSON is read and parsed on every Google link (Low)

- **Where:** `src/GoogleWallet/GoogleWalletSaveLinkBuilder.php` (`loadServiceAccount()`), called from `buildSaveLinkFromPayload()`.
- **Worker impact:** one small local file read and `json_decode()` per generated link. There is no leak: the private key only lives in local variables and is never stored in a property, which is the right choice for a long-lived worker (the key is not kept in memory between requests and key rotation is picked up immediately).
- **Recommendation:** none required. If profiling ever shows this as a hotspot, a cache must stay keyed by path and file mtime, and must not keep the private key in a shared property longer than needed.

### W-02 — QR rendering depends on QrCodeBundle (Info / resolved by constraint)

- **Where:** `WalletQrService` injects `Nowo\QrCodeBundle\Service\QrCodeService`.
- **Worker impact:** this bundle adds no state. Effective worker behaviour also depends on QrCodeBundle.
- **Status:** From WalletQrBundle **3.0.9**, Composer requires `nowo-tech/qr-code-bundle` **`^1.4.9`**, whose own audit rates it ✅ *Compatible under scenario B* for YAML and `use_database_config: true` (array hydration + closed-EM recovery; see QrCodeBundle `docs/FRANKENPHP-WORKER-AUDIT.md`).

No other findings. No code changes were required inside WalletQrBundle services to reach scenario B.

## Usage recommendations in worker mode

- No special configuration or `kernel.reset` hook is needed for this bundle under `reset_kernel` false.
- Do not store the returned `WalletQr` / `WalletLink` objects (they contain a signed, per-user JWT for Google Wallet) in a shared service property or a static cache; keep them per request.
- Mount the Google service account JSON as a read-only file; changes are picked up without restarting workers.
- Custom decorators of `WalletQrService` or the link builders must stay stateless (or implement `ResetInterface`) to keep this verdict.
- Keep `nowo-tech/qr-code-bundle` at **1.4.9+** when running under worker + no kernel reset.

## Re-audit triggers

Re-run this audit when a change adds: properties to the link builders or `WalletQrService`, a cache of the service account or of generated links, an event listener or controller, any network call to the Google Wallet API, or when the QrCodeBundle audit verdict regresses.
