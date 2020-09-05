<?php

declare(strict_types=1);

namespace Migrify\FatalErrorScanner\HttpKernel;

use Migrify\MigrifyKernel\Bundle\MigrifyKernelBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

final class FatalErrorScannerKernel extends Kernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/fatal_error_scanner';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/fatal_error_scanner_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new MigrifyKernelBundle()];
    }
}
