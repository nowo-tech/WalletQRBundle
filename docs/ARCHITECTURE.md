# Architecture

WalletQrBundle is an **infrastructure** bundle: it builds Google Wallet "save" links (a JWT signed with a service
account) and Apple Wallet pass download URLs, and renders them as QR codes through
[QrCodeBundle](https://github.com/nowo-tech/QrCodeBundle). It has **no Doctrine entities, no mapped superclasses, no
traits and dispatches no events**, so there is nothing to map and it never touches the host EntityManager
(REQ-ARCH-001 rules 2, 4, 6 and 7 do not apply). Configuration comes from the config tree (`nowo_wallet_qr`), never from
InstanceSettings (rule 8).

## Table of contents

- [Ports and extension points (SPI)](#ports-and-extension-points-spi)
- [Standard Symfony and DDD hosts](#standard-symfony-and-ddd-hosts)

## Ports and extension points (SPI)

| Interface | Shipped implementation | Service id | Implement it to |
|-----------|------------------------|------------|-----------------|
| `Service\WalletQrServiceInterface` | `WalletQrService` | `Nowo\WalletQrBundle\Service\WalletQrService` | replace or double the facade (tests, decoration) |
| `GoogleWallet\GoogleWalletSaveLinkBuilderInterface` | `GoogleWalletSaveLinkBuilder` (RS256 JWT, service account file) | `nowo_wallet_qr.google_wallet.save_link_builder` | sign elsewhere (HSM, signing service) |
| `AppleWallet\AppleWalletPassLinkBuilderInterface` | `AppleWalletPassLinkBuilder` (`{pass_id}` URL pattern) | `nowo_wallet_qr.apple_wallet.pass_link_builder` | build signed, expiring or per-tenant pass URLs |

`WalletQrServiceInterface` is aliased to the shipped class, and the Twig functions depend on it, so overriding the alias
changes both PHP and Twig callers. `WalletQrService` receives the builders by service id (optional references), so a host
plugs its own builder by registering a service under the documented id: host service definitions load after the bundle's
and win. The builders are registered by the bundle only when their platform is enabled (`google_wallet.enabled`,
`apple_wallet.enabled`), and the interface aliases exist under the same condition; a host that defines the id itself
should also alias the interface.

```yaml
# config/services.yaml
nowo_wallet_qr.apple_wallet.pass_link_builder:
    class: App\Infrastructure\Wallet\SignedPassUrls   # implements AppleWalletPassLinkBuilderInterface

Nowo\WalletQrBundle\AppleWallet\AppleWalletPassLinkBuilderInterface: '@nowo_wallet_qr.apple_wallet.pass_link_builder'
```

Value objects (`Model\WalletLink`, `Model\WalletQr`, `Model\GoogleWalletPassReference`, `Enum\WalletPlatform`) carry
scalars, enums and strings only; no Symfony or Doctrine type appears in the interfaces above. QR rendering and the URL
allowlist belong to QrCodeBundle (`nowo_wallet_qr.qr_code.*` is forwarded to it).

## Standard Symfony and DDD hosts

**Standard Symfony host (default).** Enable a platform in `nowo_wallet_qr`, inject `WalletQrServiceInterface` (or use the
Twig functions) and render the `WalletQr` result. Nothing else to wire.

**DDD / hexagonal host (opt-in).**

- Nothing to map and no base class to extend: the domain never sees the bundle.
- Define your own port in the application layer, for example `WalletPassQr::forTicket(TicketId): string` (returning the
  data URI), and implement it in infrastructure with `WalletQrServiceInterface`. Build the `GoogleWalletPassReference`
  from your ids there (`GoogleWalletPassReference::withIssuer($issuer, $objectId, $classId)`); never pass an entity.
- Catch `WalletConfigurationException` (platform disabled) and `InvalidQrUrlException` (URL not allowed) in the adapter.
- Replace a builder through its interface when pass links must be signed, expiring or per tenant; keep secrets (service
  account path, signing keys) in configuration or your secret store, never in the domain.
- Dispatch no events from the bundle: if your domain needs "pass QR issued", raise it in your own adapter after the QR
  is created, carrying the pass id.
