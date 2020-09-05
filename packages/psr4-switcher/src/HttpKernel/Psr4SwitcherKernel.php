<?php

declare(strict_types=1);

namespace Migrify\Psr4Switcher\HttpKernel;

use Migrify\MigrifyKernel\Bundle\MigrifyKernelBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ComposerJsonManipulator\ComposerJsonManipulatorBundle;

final class Psr4SwitcherKernel extends Kernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/psr4_switcher';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/psr4_switcher_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new ComposerJsonManipulatorBundle(), new MigrifyKernelBundle()];
    }
}
