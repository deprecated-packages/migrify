<?php

declare(strict_types=1);

namespace Migrify\ZephirToPhp\Tests\ZephirToPhpConverter;

use Iterator;
use Migrify\ZephirToPhp\HttpKernel\ZephirToPhpKernel;
use Migrify\ZephirToPhp\ZephirToPhpConverter;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ZephirToPhpConverterTest extends AbstractKernelTestCase
{
    /**
     * @var ZephirToPhpConverter
     */
    private $zephirToPhpConverter;

    protected function setUp(): void
    {
        $this->bootKernel(ZephirToPhpKernel::class);
        $this->zephirToPhpConverter = self::$container->get(ZephirToPhpConverter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        [$inputFileInfo, $expectedFileInfo] = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $convertedFileContent = $this->zephirToPhpConverter->convertFile($inputFileInfo);
        $this->assertSame(
            $expectedFileInfo->getContents(),
            $convertedFileContent,
            $fixtureFileInfo->getRelativeFilePathFromCwd()
        );
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.zep');
    }
}
