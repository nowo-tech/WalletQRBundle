<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Integration;

use Nowo\WalletQrBundle\DependencyInjection\NowoWalletQrExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Integration tests: DI extension registers bundle parameters and services.
 */
#[CoversClass(NowoWalletQrExtension::class)]
final class NowoWalletQrExtensionIntegrationTest extends TestCase
{
    public function testExtensionLoadsDefaultConfiguration(): void
    {
        $container = new ContainerBuilder();
        (new NowoWalletQrExtension())->load([], $container);

        self::assertTrue($container->hasParameter('nowo_wallet_qr.config'));
        self::assertFalse($container->getParameter('nowo_wallet_qr.google_wallet.enabled'));
        self::assertFalse($container->getParameter('nowo_wallet_qr.apple_wallet.enabled'));
    }

    public function testExtensionRegistersGoogleWalletBuilderWhenConfigured(): void
    {
        $container = new ContainerBuilder();
        (new NowoWalletQrExtension())->load([[
            'google_wallet' => [
                'enabled'              => true,
                'issuer_id'            => '3388000000000000000',
                'service_account_json' => __FILE__,
                'origins'              => ['example.com'],
            ],
        ]], $container);

        self::assertTrue($container->hasDefinition('nowo_wallet_qr.google_wallet.save_link_builder'));
        self::assertTrue($container->getParameter('nowo_wallet_qr.google_wallet.enabled'));

        $definition = $container->getDefinition('nowo_wallet_qr.google_wallet.save_link_builder');
        $clock      = $definition->getArgument(3);
        $logger     = $definition->getArgument(4);
        self::assertInstanceOf(Reference::class, $clock);
        self::assertSame(ClockInterface::class, (string) $clock);
        self::assertSame(ContainerInterface::NULL_ON_INVALID_REFERENCE, $clock->getInvalidBehavior());
        self::assertInstanceOf(Reference::class, $logger);
        self::assertSame('logger', (string) $logger);
        self::assertSame(ContainerInterface::NULL_ON_INVALID_REFERENCE, $logger->getInvalidBehavior());
        self::assertSame([['channel' => 'wallet_qr']], $definition->getTag('monolog.logger'));
    }

    public function testEnabledGoogleWalletWithoutIssuerFailsWithPathInMessage(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('nowo_wallet_qr.google_wallet is enabled but issuer_id is empty');

        (new NowoWalletQrExtension())->load([[
            'google_wallet' => ['enabled' => true, 'service_account_json' => __FILE__],
        ]], new ContainerBuilder());
    }
}
