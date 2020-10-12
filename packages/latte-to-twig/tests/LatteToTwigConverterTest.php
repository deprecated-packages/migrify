<?php

declare(strict_types=1);

namespace Migrify\LatteToTwig\Tests;

use Iterator;
use Migrify\LatteToTwig\HttpKernel\LatteToTwigKernel;
use Migrify\LatteToTwig\LatteToTwigConverter;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LatteToTwigConverterTest extends AbstractKernelTestCase
{
    /**
     * @var LatteToTwigConverter
     */
    private $latteToTwigConverter;

    protected function setUp(): void
    {
        $this->bootKernel(LatteToTwigKernel::class);
        $this->latteToTwigConverter = self::$container->get(LatteToTwigConverter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputFileInfoAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $convertedContent = $this->latteToTwigConverter->convertFile(
            $inputFileInfoAndExpectedFileInfo->getInputFileInfo()
        );

        $this->assertSame(
            $inputFileInfoAndExpectedFileInfo->getExpectedFileInfo()
                ->getContents(),
            $convertedContent,
            $fixtureFileInfo->getRelativeFilePathFromCwd()
        );
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.latte');
    }
}
