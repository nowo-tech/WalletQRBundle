<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\GoogleWallet;

use Firebase\JWT\JWT;
use Nowo\WalletQrBundle\Exception\WalletConfigurationException;
use Nowo\WalletQrBundle\Model\GoogleWalletPassReference;

use function is_array;
use function is_string;
use function json_decode;
use function sprintf;
use function trim;

/**
 * Builds signed Google Wallet "Add to Google Wallet" save links.
 *
 * @see https://developers.google.com/wallet/generic/web
 */
final class GoogleWalletSaveLinkBuilder
{
    private const SAVE_URL = 'https://pay.google.com/gp/v/save/';

    /**
     * @param list<string> $origins
     */
    public function __construct(
        private readonly string $issuerId,
        private readonly string $serviceAccountJsonPath,
        private readonly array $origins = [],
    ) {
    }

    /**
     * @param list<string>|null $origins
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
     * @param array<string, mixed> $payload
     * @param list<string>|null $origins
     */
    public function buildSaveLinkFromPayload(array $payload, ?array $origins = null): string
    {
        $serviceAccount = $this->loadServiceAccount();
        $claims         = [
            'iss'     => $serviceAccount['client_email'],
            'aud'     => 'google',
            'typ'     => 'savetowallet',
            'iat'     => time(),
            'payload' => $payload,
        ];

        $effectiveOrigins = $origins ?? $this->origins;
        if ($effectiveOrigins !== []) {
            $claims['origins'] = $effectiveOrigins;
        }

        $token = JWT::encode($claims, $serviceAccount['private_key'], 'RS256');

        return self::SAVE_URL . $token;
    }

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
