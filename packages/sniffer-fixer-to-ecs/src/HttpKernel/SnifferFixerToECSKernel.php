<?php

declare(strict_types=1);

namespace Migrify\SnifferFixerToECS\HttpKernel;

use Migrify\MigrifyKernel\Bundle\MigrifyKernelBundle;
use Migrify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

final class SnifferFixerToECSKernel extends Kernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/sniffer_fixer_to_ecs';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/sniffer_fixer_to_ecs_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new PhpConfigPrinterBundle(), new MigrifyKernelBundle()];
    }
}
