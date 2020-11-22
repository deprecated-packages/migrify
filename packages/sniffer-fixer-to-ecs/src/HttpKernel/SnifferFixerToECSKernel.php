<?php

declare(strict_types=1);

namespace Migrify\SnifferFixerToECS\HttpKernel;

use Migrify\MigrifyKernel\Bundle\MigrifyKernelBundle;
use Migrify\MigrifyKernel\HttpKernel\AbstractMigrifyKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle;

final class SnifferFixerToECSKernel extends AbstractMigrifyKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new PhpConfigPrinterBundle(), new MigrifyKernelBundle()];
    }
}
