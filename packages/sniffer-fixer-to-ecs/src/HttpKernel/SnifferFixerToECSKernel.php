<?php

declare(strict_types=1);

namespace Migrify\SnifferFixerToECS\HttpKernel;

use Migrify\MigrifyKernel\Bundle\MigrifyKernelBundle;
use Migrify\MigrifyKernel\HttpKernel\AbstractMigrifyKernel;
use Migrify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

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
