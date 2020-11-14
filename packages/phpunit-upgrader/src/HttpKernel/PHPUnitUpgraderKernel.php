<?php

declare(strict_types=1);

namespace Migrify\PHPUnitUpgrader\HttpKernel;

use Migrify\MigrifyKernel\Bundle\MigrifyKernelBundle;
use Migrify\MigrifyKernel\HttpKernel\AbstractMigrifyKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\ConsoleColorDiff\ConsoleColorDiffBundle;

final class PHPUnitUpgraderKernel extends AbstractMigrifyKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new ConsoleColorDiffBundle(), new MigrifyKernelBundle()];
    }
}
