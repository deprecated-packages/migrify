<?php

declare(strict_types=1);

namespace Migrify\ConfigClassPresence\Tests\Regex\ClassExtractor;

use Migrify\ConfigClassPresence\HttpKernel\ConfigClassPresenceKernel;
use Migrify\ConfigClassPresence\Regex\ClassExtractor;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassExtractorTest extends AbstractKernelTestCase
{
    /**
     * @var ClassExtractor
     */
    private $classExtractor;

    protected function setUp(): void
    {
        $this->bootKernel(ConfigClassPresenceKernel::class);

        $this->classExtractor = self::$container->get(ClassExtractor::class);
    }

    public function test(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Source/some_config.neon');

        $classes = $this->classExtractor->extractFromFileInfo($fileInfo);
        $this->assertCount(1, $classes);
    }
}
