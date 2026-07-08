<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Security;

use Nowo\WalletQrBundle\Exception\InvalidWalletQrUrlException;

use function in_array;
use function is_string;
use function preg_match;
use function sprintf;
use function str_contains;
use function str_starts_with;
use function strtolower;

use const PHP_URL_HOST;

/**
 * Validates URLs encoded into QR codes (http/https only; blocks javascript/data schemes).
 */
final class QrUrlPolicy
{
    /**
     * @param list<string> $hostAllowlist Substring patterns or regex (prefix #). Empty = any public http(s) host.
     */
    public function __construct(
        private readonly array $hostAllowlist = [],
    ) {
    }

    public function assertAllowed(string $url): void
    {
        if (!$this->isAllowed($url)) {
            throw new InvalidWalletQrUrlException(sprintf('URL is not allowed for QR encoding: %s', $url));
        }
    }

    public function isAllowed(string $url): bool
    {
        $scheme = $this->extractScheme($url);
        if ($scheme === null || !in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (!is_string($host) || $host === '') {
            return false;
        }

        return $this->isAllowedHost($host, $url);
    }

    private function isAllowedHost(string $host, string $url): bool
    {
        if ($this->hostAllowlist === []) {
            return true;
        }

        foreach ($this->hostAllowlist as $pattern) {
            if ($pattern === '') {
                continue;
            }
            if (str_starts_with($pattern, '#')) {
                if (preg_match($pattern, $host) === 1 || preg_match($pattern, $url) === 1) {
                    return true;
                }
                continue;
            }
            if (str_contains($host, $pattern) || str_contains($url, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function extractScheme(string $url): ?string
    {
        if (!preg_match('#^([a-z][a-z0-9+.-]*):#i', $url, $matches)) {
            return null;
        }

        return strtolower($matches[1]);
    }
}
