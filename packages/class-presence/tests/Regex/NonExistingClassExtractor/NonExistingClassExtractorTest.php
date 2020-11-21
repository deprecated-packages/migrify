<?php

declare(strict_types=1);

namespace Migrify\ClassPresence\Tests\Regex\NonExistingClassExtractor;

use Iterator;
use Migrify\ClassPresence\HttpKernel\ClassPresenceKernel;
use Migrify\ClassPresence\Regex\NonExistingClassExtractor;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NonExistingClassExtractorTest extends AbstractKernelTestCase
{
    /**
     * @var NonExistingClassExtractor
     */
    private $nonExistingClassExtractor;

    protected function setUp(): void
    {
        $this->bootKernel(ClassPresenceKernel::class);
        $this->nonExistingClassExtractor = self::$container->get(NonExistingClassExtractor::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath, int $expectedClassCount): void
    {
        $fileInfo = new SmartFileInfo($filePath);

        $nonExistingClasses = $this->nonExistingClassExtractor->extractFromFileInfo($fileInfo);
        $this->assertCount($expectedClassCount, $nonExistingClasses);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/config/some_config.neon', 1];
        yield [__DIR__ . '/Fixture/config/static_call.neon', 1];

        yield [__DIR__ . '/Fixture/config/mapping_only.neon', 0];
        yield [__DIR__ . '/Fixture/config/skip_psr4_autodiscovery.yaml', 0];

        yield [__DIR__ . '/Fixture/template/file.latte', 2];
        yield [__DIR__ . '/Fixture/template/file_with_existing_class.latte', 0];
        yield [__DIR__ . '/Fixture/template/different_file.twig', 1];

        // blade, laravel
        yield [__DIR__ . '/Fixture/template/non_existing_in_blade_file.php', 3];
        yield [__DIR__ . '/Fixture/template/existing_in_blade_file.php', 0];
    }
}
