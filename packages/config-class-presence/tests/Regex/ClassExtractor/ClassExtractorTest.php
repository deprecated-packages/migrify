<?php

declare(strict_types=1);

namespace Migrify\ConfigClassPresence\Tests\Regex\ClassExtractor;

use Iterator;
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

    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath, int $expectedClassCount): void
    {
        $fileInfo = new SmartFileInfo($filePath);

        $classes = $this->classExtractor->extractFromFileInfo($fileInfo);
        $this->assertCount($expectedClassCount, $classes);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Source/some_config.neon', 1];
        yield [__DIR__ . '/Source/static_call.neon', 1];
    }
}
