<?php

declare(strict_types=1);

namespace Migrify\ConfigClassPresence\Tests\Finder\ConfigFinder;

use Migrify\ConfigClassPresence\Finder\ConfigFinder;
use Migrify\ConfigClassPresence\HttpKernel\ConfigClassPresenceKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class ConfigFinderTest extends AbstractKernelTestCase
{
    /**
     * @var ConfigFinder
     */
    private $configFinder;

    protected function setUp(): void
    {
        $this->bootKernel(ConfigClassPresenceKernel::class);
        $this->configFinder = self::$container->get(ConfigFinder::class);
    }

    public function test(): void
    {
        $configFileInfos = $this->configFinder->findIn([__DIR__ . '/Source/']);
        $this->assertCount(2, $configFileInfos);
    }
}
