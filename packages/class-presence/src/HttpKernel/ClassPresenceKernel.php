<?php

declare(strict_types=1);

namespace Migrify\ClassPresence\HttpKernel;

use Migrify\MigrifyKernel\Bundle\MigrifyKernelBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

final class ClassPresenceKernel extends Kernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/diff_data_miner';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/diff_data_miner_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new MigrifyKernelBundle()];
    }
}
