# Wallet QR Bundle — Demos

Two FrankenPHP demo applications show the Wallet QR Bundle (Google Wallet / Apple Wallet save links with QR codes) in a Symfony **8.1** app. Development vs production start, `FRANKENPHP_MODE` (worker / classic) and troubleshooting are documented in [docs/DEMO-FRANKENPHP.md](../docs/DEMO-FRANKENPHP.md).

## Table of contents

- [Demo projects](#demo-projects)
- [Quick start](#quick-start)
- [What each demo includes](#what-each-demo-includes)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)

## Demo projects

| Demo | Symfony | PHP image | Default port | Compose project |
| --- | --- | --- | --- | --- |
| `symfony8/` | 8.1 | `dunglas/frankenphp:1-php8.5-alpine` | **8003** | `wallet-qr-bundle-demo-symfony-8` |
| `symfony8-php85/` | 8.1 | `dunglas/frankenphp:1-php8.5-alpine` | **8004** | `wallet-qr-bundle-demo-symfony-8-php85` |

Ports come from each demo's `.env` (copied from `.env.example` on first `up`).

## Quick start

```bash
make -C demo up-symfony8          # build + start, composer install
make -C demo logs-symfony8        # follow FrankenPHP logs
make -C demo down-symfony8        # stop

make -C demo up-symfony8-php85    # second demo
make -C demo help                 # every target (install-, shell-, test-, test-coverage-<demo>, verify-all, demo-smoke, release-check)
```

Open `http://localhost:<PORT>/` (route `demo_home`).

## What each demo includes

- `DemoController` rendering wallet save links and their QR codes (QR rendering via the mandatory `nowo-tech/qr-code-bundle`).
- A single FrankenPHP service (Caddy on HTTP :80, worker mode by default; `Caddyfile.dev` for `FRANKENPHP_MODE=classic`), no nginx.
- Web Profiler, Nowo Twig Inspector and Nowo Hot Reload in `dev`.
- The parent bundle mounted from the repository root (path repository), so local `src/` changes are visible after `make -C demo update-bundle-all`.

## Testing

```bash
make -C demo test-symfony8        # PHPUnit inside the demo container
make -C demo test-all             # every demo
make -C demo demo-smoke           # REQ-TEST-011: boot symfony8 and assert HTTP 200
make -C demo release-check        # pre-release: tests + up → HTTP 200 → down for each demo
```

## Troubleshooting

| Symptom | Fix |
| --- | --- |
| Port already in use | Change `PORT` in the demo `.env` and run `make -C demo up-<demo>` again. |
| Composer cannot resolve the bundle | The demos require the mounted bundle through a path repository with an inline alias; run from inside this repository so the relative path resolves. |
| Changes to Twig/PHP do not appear | Worker mode keeps the kernel in memory: set `FRANKENPHP_MODE=classic` in `.env` and recreate the container (`docker compose up -d`), or restart it. |
| Routes not loading | Clear the cache: `make -C demo shell-<demo>` then `php bin/console cache:clear`. |

More: [docs/DEMO-FRANKENPHP.md](../docs/DEMO-FRANKENPHP.md).
