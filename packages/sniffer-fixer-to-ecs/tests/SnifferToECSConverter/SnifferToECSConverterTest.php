<?php

declare(strict_types=1);

namespace Migrify\SnifferFixerToECS\Tests\SnifferToECSConverter;

use Iterator;
use Migrify\SnifferFixerToECS\HttpKernel\SnifferFixerToECSKernel;
use Migrify\SnifferFixerToECS\SnifferToECSConverter;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\DataProvider\StaticFixtureUpdater;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SnifferToECSConverterTest extends AbstractKernelTestCase
{
    /**
     * @var SnifferToECSConverter
     */
    private $snifferToECSConverter;

    protected function setUp(): void
    {
        $this->bootKernel(SnifferFixerToECSKernel::class);
        $this->snifferToECSConverter = self::$container->get(SnifferToECSConverter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $convertedContent = $this->snifferToECSConverter->convertFile($inputAndExpectedFileInfo->getInputFileInfo());

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
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.xml');
    }
}
