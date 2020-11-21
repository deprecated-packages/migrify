<?php

declare(strict_types=1);

namespace Migrify\SnifferFixerToECS\Tests\FixerToECSConverter;

use Iterator;
use Migrify\SnifferFixerToECS\FixerToECSConverter;
use Migrify\SnifferFixerToECS\HttpKernel\SnifferFixerToECSKernel;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\DataProvider\StaticFixtureUpdater;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FixerToECSConverterTest extends AbstractKernelTestCase
{
    /**
     * @var FixerToECSConverter
     */
    private $fixerToECSConverter;

    protected function setUp(): void
    {
        $this->bootKernel(SnifferFixerToECSKernel::class);
        $this->fixerToECSConverter = self::$container->get(FixerToECSConverter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $convertedContent = $this->fixerToECSConverter->convertFile($inputAndExpectedFileInfo->getInputFileInfo());

        StaticFixtureUpdater::updateFixtureContent(
            $inputAndExpectedFileInfo->getInputFileInfo(),
            $convertedContent,
            $fixtureFileInfo
        );

        $this->assertSame(
            $inputAndExpectedFileInfo->getExpectedFileInfo()
                ->getContents(),
            $convertedContent,
            $fixtureFileInfo->getRelativeFilePathFromCwd()
        );
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.dist');
    }
}
