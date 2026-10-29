<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Unit\GoogleWallet;

use Psr\Log\AbstractLogger;
use Stringable;

/**
 * In-memory PSR-3 logger for assertions.
 */
final class InMemoryLogger extends AbstractLogger
{
    /** @var list<array{level: mixed, message: string, context: array<string, mixed>}> */
    public array $records = [];

    /**
     * @param array<string, mixed> $context
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->records[] = ['level' => $level, 'message' => (string) $message, 'context' => $context];
    }
}
