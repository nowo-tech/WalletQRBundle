<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Model;

use function sprintf;

/**
 * Reference to an existing Google Wallet pass class and object.
 */
final readonly class GoogleWalletPassReference
{
    /**
     * Creates a pass reference from fully qualified IDs.
     *
     * @param string $objectId Fully qualified pass object ID (`<issuerId>.<suffix>`)
     * @param string $classId Fully qualified pass class ID (`<issuerId>.<suffix>`)
     */
    public function __construct(
        public string $objectId,
        public string $classId,
    ) {
    }

    /**
     * Builds fully qualified IDs using the Google Wallet issuer ID.
     *
     * @param string $issuerId Google Wallet issuer ID
     * @param string $objectSuffix Object ID suffix
     * @param string $classSuffix Class ID suffix
     */
    public static function withIssuer(string $issuerId, string $objectSuffix, string $classSuffix): self
    {
        return new self(
            objectId: sprintf('%s.%s', $issuerId, $objectSuffix),
            classId: sprintf('%s.%s', $issuerId, $classSuffix),
        );
    }
}
