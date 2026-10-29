<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\GoogleWallet;

use Firebase\JWT\JWT;
use Nowo\WalletQrBundle\Exception\WalletConfigurationException;
use Nowo\WalletQrBundle\Model\GoogleWalletPassReference;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Clock\NativeClock;
use Throwable;

use function count;
use function is_array;
use function is_string;
use function json_decode;
use function sprintf;
use function trim;

/**
 * Builds signed Google Wallet "Add to Google Wallet" save links.
 *
 * The JWT "iat" claim comes from the injected PSR-20 clock and every signing attempt is
 * logged through the injected PSR-3 logger without secrets (no private key, no JWT, no
 * payload: only the issuer id, the number of origins and the failure reason).
 *
 * @see https://developers.google.com/wallet/generic/web
 */
final readonly class GoogleWalletSaveLinkBuilder
{
    private const SAVE_URL = 'https://pay.google.com/gp/v/save/';

    private ClockInterface $clock;

    private LoggerInterface $logger;

    /**
     * Creates the save link builder.
     *
     * @param string $issuerId Google Wallet issuer ID
     * @param string $serviceAccountJsonPath Absolute path to the service account JSON key
     * @param list<string> $origins Default JWT origins allowed to initiate the save
     * @param ClockInterface|null $clock Clock used for the JWT "iat" claim (system clock when null)
     * @param LoggerInterface|null $logger Logger for signing start/success/failure (no-op when null)
     */
    public function __construct(
        private string $issuerId,
        private string $serviceAccountJsonPath,
        private array $origins = [],
        ?ClockInterface $clock = null,
        ?LoggerInterface $logger = null,
    ) {
        $this->clock  = $clock ?? new NativeClock();
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Builds a save link for an existing Google Wallet generic class/object pair.
     *
     * @param GoogleWalletPassReference $reference Pass class and object IDs
     * @param list<string>|null $origins JWT origins (null = configured default)
     *
     * @return string The https://pay.google.com save URL
     */
    public function buildSaveLink(GoogleWalletPassReference $reference, ?array $origins = null): string
    {
        return $this->buildSaveLinkFromPayload([
            'genericObjects' => [[
                'id'      => $reference->objectId,
                'classId' => $reference->classId,
            ]],
        ], $origins);
    }

    /**
     * Builds a save link from a raw Google Wallet JWT payload.
     *
     * @param array<string, mixed> $payload Google Wallet "payload" claim
     * @param list<string>|null $origins JWT origins (null = configured default)
     *
     * @throws WalletConfigurationException when the service account is missing or invalid
     *
     * @return string The https://pay.google.com save URL
     */
    public function buildSaveLinkFromPayload(array $payload, ?array $origins = null): string
    {
        $effectiveOrigins = $origins ?? $this->origins;
        $context          = [
            'bundle'    => 'wallet_qr',
            'action'    => 'google_wallet.sign_save_link',
            'issuer_id' => $this->issuerId,
            'origins'   => count($effectiveOrigins),
        ];

        $this->logger->debug('Signing Google Wallet save link.', $context);

        try {
            $serviceAccount = $this->loadServiceAccount();
            $claims         = [
                'iss'     => $serviceAccount['client_email'],
                'aud'     => 'google',
                'typ'     => 'savetowallet',
                'iat'     => $this->clock->now()->getTimestamp(),
                'payload' => $payload,
            ];

            if ($effectiveOrigins !== []) {
                $claims['origins'] = $effectiveOrigins;
            }

            $token = JWT::encode($claims, $serviceAccount['private_key'], 'RS256');
        } catch (WalletConfigurationException $e) {
            $this->logger->error('Google Wallet save link not signed: configuration error.', $context + ['reason' => $e->getMessage()]);

            throw $e;
        } catch (Throwable $e) {
            // Never log the message of a signing error: it may quote key material.
            $this->logger->error('Google Wallet save link not signed: JWT signing failed.', $context + ['exception_class' => $e::class]);

            throw $e;
        }

        $this->logger->info('Google Wallet save link signed.', $context);

        return self::SAVE_URL . $token;
    }

    /**
     * Tells whether both the issuer ID and the service account path are non-empty.
     *
     * @param string $issuerId Google Wallet issuer ID
     * @param string $serviceAccountJsonPath Path to the service account JSON key
     */
    public static function isConfigured(string $issuerId, string $serviceAccountJsonPath): bool
    {
        return trim($issuerId) !== '' && trim($serviceAccountJsonPath) !== '';
    }

    /**
     * @return array{client_email: string, private_key: string}
     */
    private function loadServiceAccount(): array
    {
        if (trim($this->issuerId) === '') {
            throw new WalletConfigurationException('Google Wallet issuer_id is not configured.');
        }

        if (trim($this->serviceAccountJsonPath) === '') {
            throw new WalletConfigurationException('Google Wallet service_account_json path is not configured.');
        }

        if (!is_file($this->serviceAccountJsonPath)) {
            throw new WalletConfigurationException(sprintf('Google Wallet service account file not found at "%s".', $this->serviceAccountJsonPath));
        }

        $decoded = json_decode((string) file_get_contents($this->serviceAccountJsonPath), true);
        if (!is_array($decoded)) {
            throw new WalletConfigurationException('Google Wallet service account JSON is invalid.');
        }

        $clientEmail = $decoded['client_email'] ?? null;
        $privateKey  = $decoded['private_key'] ?? null;

        if (!is_string($clientEmail) || trim($clientEmail) === '') {
            throw new WalletConfigurationException('Google Wallet service account JSON is missing client_email.');
        }

        if (!is_string($privateKey) || trim($privateKey) === '') {
            throw new WalletConfigurationException('Google Wallet service account JSON is missing private_key.');
        }

        return [
            'client_email' => $clientEmail,
            'private_key'  => $privateKey,
        ];
    }
}
