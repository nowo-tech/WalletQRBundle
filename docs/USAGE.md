# Usage

## Inject the service

```php
use Nowo\WalletQrBundle\Model\GoogleWalletPassReference;
use Nowo\WalletQrBundle\Service\WalletQrService;

final class TicketController
{
    public function __construct(
        private readonly WalletQrService $walletQrService,
    ) {
    }
}
```

## Google Wallet QR (Android)

Build a save link for an existing pass class/object pair:

```php
$reference = GoogleWalletPassReference::withIssuer(
    issuerId: '3388000000000000000',
    objectSuffix: 'TICKET_123',
    classSuffix: 'EVENT_TICKET',
);

$androidQr = $this->walletQrService->createGoogleWalletQr($reference);

echo $androidQr->link->url;
echo $androidQr->qrCodeDataUri;
```

## Apple Wallet QR (iOS)

```php
$iosQr = $this->walletQrService->createAppleWalletQr('TICKET_123');

echo $iosQr->link->url;
```

## Android + iOS pair

```php
$pair = $this->walletQrService->createWalletQrPair(
    googleReference: GoogleWalletPassReference::withIssuer($issuerId, 'TICKET_123', 'EVENT_TICKET'),
    applePassId: 'TICKET_123',
);
```

## Custom URL QR

```php
use Nowo\WalletQrBundle\Enum\WalletPlatform;

$qr = $this->walletQrService->createQrForUrl(
    WalletPlatform::Ios,
    'https://cdn.example.com/passes/event-123.pkpass',
);
```

## Twig

```twig
<img src="{{ wallet_qr_data_uri(androidQr) }}" alt="Add to Google Wallet">

{% set iosQr = wallet_qr_for_url('ios', passDownloadUrl) %}
<img src="{{ wallet_qr_data_uri(iosQr) }}" alt="Add to Apple Wallet">
```

## Mobile scanning notes

- **Android**: scanning opens the Google Wallet save flow.
- **iOS**: scanning should open Safari and download/add the `.pkpass` pass.
- Keep Google Wallet JWT URLs under ~1800 characters to avoid browser truncation.
