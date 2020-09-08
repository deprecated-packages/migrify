<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\HttpKernel;

use Migrify\MigrifyKernel\Bundle\MigrifyKernelBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;

final class StaticDetectorKernel extends Kernel implements ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');

        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/static_detector';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/static_detector_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new MigrifyKernelBundle()];
    }

    /**
     * @param string[] $configs
     */
    public function setConfigs(array $configs): void
    {
        $this->configs = $configs;
    }
}
