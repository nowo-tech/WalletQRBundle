# Configuration

All options live under the `nowo_wallet_qr` root key.

## Table of contents

- [Google Wallet (Android)](#google-wallet-android)
- [Apple Wallet (iOS)](#apple-wallet-ios)
- [QR code rendering](#qr-code-rendering)
- [Environment variables](#environment-variables)

## Google Wallet (Android)

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `google_wallet.enabled` | bool | `false` | Enable JWT save-link generation |
| `google_wallet.issuer_id` | string | `''` | Google Wallet issuer ID |
| `google_wallet.service_account_json` | string | `''` | Absolute path to service account JSON |
| `google_wallet.origins` | string[] | `[]` | Optional JWT `origins` claim |

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

Example:

```yaml
nowo_wallet_qr:
    apple_wallet:
        enabled: true
        pass_download_url_pattern: 'https://www.example.com/wallet/{pass_id}.pkpass'
```

Your application must serve the `.pkpass` file at the resolved URL with the correct `application/vnd.apple.pkpass` content type.

## QR code rendering

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `qr_code.size` | int | `300` | Image size in pixels (64–1024) |
| `qr_code.margin` | int | `10` | Quiet zone margin |
| `qr_code.error_correction` | string | `high` | `low`, `medium`, `quartile`, or `high` |
| `qr_code.url_allowlist` | string[] | `[]` | Optional host/URL patterns for `createQrForUrl` / `wallet_qr_for_url` (substring or `#regex`). Empty = any `http`/`https` URL with a valid host. |

Example with URL allowlist:

```yaml
nowo_wallet_qr:
    qr_code:
        size: 300
        url_allowlist:
            - example.com
            - '#^https://cdn\\.example\\.com/'
```

## Environment variables

Recommended secrets handling:

```dotenv
GOOGLE_WALLET_ISSUER_ID=3388000000000000000
```

Store the service account JSON outside the web root and reference it via `service_account_json`.
