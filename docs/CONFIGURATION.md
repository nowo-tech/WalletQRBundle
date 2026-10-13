# Configuration

All options live under the `nowo_wallet_qr` root key.

## Table of contents

- [Google Wallet (Android)](#google-wallet-android)
- [Apple Wallet (iOS)](#apple-wallet-ios)
- [QR code rendering](#qr-code-rendering)
- [Allowlist scope](#allowlist-scope)
- [Environment variables](#environment-variables)

## Google Wallet (Android)

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `google_wallet.enabled` | bool | `false` | Enable JWT save-link generation |
| `google_wallet.issuer_id` | string | `''` | Google Wallet issuer ID |
| `google_wallet.service_account_json` | string | `''` | Absolute path to service account JSON |
| `google_wallet.origins` | string[] | `[]` | Optional JWT `origins` claim |

With `enabled: true`, both `issuer_id` and `service_account_json` must be non-empty, otherwise the container fails to compile with `InvalidConfigurationException` (it used to disable the integration silently).

Example:

```yaml
nowo_wallet_qr:
    google_wallet:
        enabled: true
        issuer_id: '%env(GOOGLE_WALLET_ISSUER_ID)%'
        service_account_json: '%kernel.project_dir%/config/google-wallet-service-account.json'
        origins:
            - www.example.com
```

## Apple Wallet (iOS)

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `apple_wallet.enabled` | bool | `false` | Enable pass download URL builder |
| `apple_wallet.pass_download_url_pattern` | string | `''` | URL with `{pass_id}` placeholder |

With `enabled: true`, an empty pattern or one without `{pass_id}` fails container compilation with `InvalidConfigurationException` (it used to disable the integration silently). A pattern from `%env()%` is only checked at runtime, when the first link is built (`WalletConfigurationException`).

Example:

```yaml
nowo_wallet_qr:
    apple_wallet:
        enabled: true
        pass_download_url_pattern: 'https://www.example.com/wallet/{pass_id}.pkpass'
```

Your application must serve the `.pkpass` file at the resolved URL with the correct `application/vnd.apple.pkpass` content type.

## QR code rendering

QR PNG rendering lives in the **required** dependency [QrCodeBundle](https://github.com/nowo-tech/QrCodeBundle). Preferred config root: **`nowo_qr_code`**.

Legacy keys under `nowo_wallet_qr.qr_code` are still supported and forwarded to `nowo_qr_code`.

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `qr_code.size` | int | `300` | Image size in pixels (64–1024) |
| `qr_code.margin` | int | `10` | Quiet zone margin |
| `qr_code.error_correction` | string | `high` | `low`, `medium`, `quartile`, or `high` |
| `qr_code.url_allowlist` | string[] | `[]` | Optional host/URL patterns for `createQrForUrl` / `wallet_qr_for_url` **only** (see [Allowlist scope](#allowlist-scope)). Empty = any `http`/`https` URL. In production, set real hosts: QrCodeBundle `^1.4.6` fails compile when `url_allowlist_required` is true and the list is empty or only `example.com`. |

Preferred:

```yaml
nowo_qr_code:
    default_profile: default
    profiles:
        default:
            size: 300
            margin: 10
            error_correction: high
            url_allowlist:
                - example.com
```

Flat keys (`size`, `margin`, …) are also accepted and normalized into `profiles.default` by QrCodeBundle.

Example with URL allowlist (legacy key):

```yaml
nowo_wallet_qr:
    qr_code:
        size: 300
        url_allowlist:
            - example.com
            - '#^https://cdn\\.example\\.com/'
```

## Allowlist scope

`url_allowlist` (`nowo_qr_code.profiles.*.url_allowlist`, or legacy `nowo_wallet_qr.qr_code.url_allowlist`) is enforced **only** for arbitrary URLs: `WalletQrService::createQrForUrl()` and the Twig `wallet_qr_for_url()`.

Links built by the bundle from trusted configuration are encoded **without** the allowlist check:

- `createGoogleWalletQr()` → always `https://pay.google.com/gp/v/save/<signed JWT>`;
- `createAppleWalletQr()` → your `apple_wallet.pass_download_url_pattern` with the URL-encoded pass id.

So `pay.google.com` / your pass host do **not** need to be in the allowlist for those methods (listing them is harmless). Do not pass user-supplied URLs to `createQrForUrl()` / `wallet_qr_for_url()` without an allowlist.

## Environment variables

Recommended secrets handling:

```dotenv
GOOGLE_WALLET_ISSUER_ID=3388000000000000000
```

Store the service account JSON outside the web root and reference it via `service_account_json`.
