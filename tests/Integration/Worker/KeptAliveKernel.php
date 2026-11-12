<?php

declare(strict_types=1);

namespace Nowo\WalletQrBundle\Tests\Integration\Worker;

use Nowo\QrCodeBundle\NowoQrCodeBundle;
use Nowo\WalletQrBundle\NowoWalletQrBundle;
use Nowo\WalletQrBundle\Service\WalletQrService;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

use function dirname;
use function sys_get_temp_dir;

/**
 * Minimal kernel for the kept-alive worker test: Google and Apple Wallet both enabled.
 */
final class KeptAliveKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new NowoQrCodeBundle();
        yield new NowoWalletQrBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(static function (ContainerBuilder $container): void {
            // The service is private and unused in this kernel: expose it for the test.
            $container->setAlias('test.wallet_qr', WalletQrService::class)->setPublic(true);
            $container->setAlias('test.apple_builder', 'nowo_wallet_qr.apple_wallet.pass_link_builder')->setPublic(true);
            $container->setAlias('test.google_builder', 'nowo_wallet_qr.google_wallet.save_link_builder')->setPublic(true);
            $container->loadFromExtension('framework', ['secret' => 'test', 'test' => true, 'http_method_override' => false, 'php_errors' => ['log' => false]]);
            $container->loadFromExtension('nowo_qr_code', ['default_profile' => 'default', 'profiles' => ['default' => ['size' => 600, 'margin' => 4]]]);
            $container->loadFromExtension('nowo_wallet_qr', [
                'google_wallet' => [
                    'enabled'              => true,
                    'issuer_id'            => '3388000000000000000',
                    'service_account_json' => dirname(__DIR__, 2) . '/fixtures/google-service-account.json',
                    'origins'              => ['example.com'],
                ],
                'apple_wallet' => [
                    'enabled'                   => true,
                    'pass_download_url_pattern' => 'https://passes.example.com/{pass_id}.pkpass',
                ],
            ]);
        });
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/wallet_qr_kept_alive/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/wallet_qr_kept_alive/log';
    }
}
