<?php

declare(strict_types=1);

namespace Migrify\MigrifyKernel\HttpKernel;

use Migrify\MigrifyKernel\Bundle\MigrifyKernelBundle;
use Nette\Utils\Strings;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

abstract class AbstractMigrifyKernel extends Kernel
{
    public function getUniqueKernelHash(): string
    {
        $finalKernelClass = static::class;
        $shortClassName = Strings::after($finalKernelClass, '//', -1);
        return Strings::lower($shortClassName);
    }

    public function getCacheDir(): string
    {
        dump($this->getUniqueKernelHash());
        die;

        return sys_get_temp_dir() . '/' . $this->getUniqueKernelHash();
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/' . $this->getUniqueKernelHash() . '_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new MigrifyKernelBundle()];
    }
}
