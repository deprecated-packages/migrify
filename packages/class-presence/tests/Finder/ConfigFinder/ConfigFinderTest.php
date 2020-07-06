<?php

declare(strict_types=1);

namespace Migrify\ClassPresence\Tests\Finder\ConfigFinder;

use Migrify\ClassPresence\Finder\FileFinder;
use Migrify\ClassPresence\HttpKernel\ClassPresenceKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class ConfigFinderTest extends AbstractKernelTestCase
{
    /**
     * @var FileFinder
     */
    private $fileFinder;

    protected function setUp(): void
    {
        $this->bootKernel(ClassPresenceKernel::class);
        $this->fileFinder = self::$container->get(FileFinder::class);
    }

    public function test(): void
    {
        $configFileInfos = $this->fileFinder->findInDirectories([__DIR__ . '/Source/']);
        $this->assertCount(2, $configFileInfos);
    }
}
