<?php

declare(strict_types=1);

namespace Migrify\MigrifyKernel\Tests\HttpKernel;

use Migrify\MigrifyKernel\Tests\HttpKernel\Source\DummyMigrifyKernel;
use Symfony\Component\Console\Application;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class MigrifyKernelTest extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernel(DummyMigrifyKernel::class);
    }

    public function test(): void
    {
        $consoleApplication = self::$container->get(Application::class);
        $this->assertInstanceOf(Application::class, $consoleApplication);
    }
}
