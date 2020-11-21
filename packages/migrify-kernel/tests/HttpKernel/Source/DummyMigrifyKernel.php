<?php

declare(strict_types=1);

namespace Migrify\MigrifyKernel\Tests\HttpKernel\Source;

use Migrify\MigrifyKernel\HttpKernel\AbstractMigrifyKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

final class DummyMigrifyKernel extends AbstractMigrifyKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
    }
}
