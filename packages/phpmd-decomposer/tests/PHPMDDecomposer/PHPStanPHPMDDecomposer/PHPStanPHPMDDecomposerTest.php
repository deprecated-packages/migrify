<?php

declare(strict_types=1);

namespace Migrify\PHPMDDecomposer\Tests\PHPMDDecomposer\PHPStanPHPMDDecomposer;

use Iterator;
use Migrify\PHPMDDecomposer\HttpKernel\PHPMDDecomposerKernel;
use Migrify\PHPMDDecomposer\PHPMDDecomposer\PHPStanConfigFactory;
use Migrify\PHPMDDecomposer\Printer\PHPStanPrinter;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\DataProvider\StaticFixtureUpdater;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PHPStanPHPMDDecomposerTest extends AbstractKernelTestCase
{
    /**
     * @var PHPStanConfigFactory
     */
    private $phpStanConfigFactory;

    /**
     * @var PHPStanPrinter
     */
    private $phpStanPrinter;

    protected function setUp(): void
    {
        self::bootKernel(PHPMDDecomposerKernel::class);
        $this->phpStanConfigFactory = self::$container->get(PHPStanConfigFactory::class);
        $this->phpStanPrinter = self::$container->get(PHPStanPrinter::class);
    }

    /**
     * For more on this testing workflow @see https://github.com/symplify/easy-testing
     *
     * @dataProvider provideDataForPHPStan()
     */
    public function testPHPStan(SmartFileInfo $fixtureFileInfo): void
    {
        $inputFileInfoAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $phpStanConfig = $this->phpStanConfigFactory->decompose(
            $inputFileInfoAndExpectedFileInfo->getInputFileInfo()
        );

        $this->assertFalse($phpStanConfig->isEmpty());

        $phpstanFileContent = $this->phpStanPrinter->printPHPStanConfig($phpStanConfig);

        // here we update test fixture if the content changed
        StaticFixtureUpdater::updateFixtureContent(
            $inputFileInfoAndExpectedFileInfo->getInputFileInfo(),
            $phpstanFileContent,
            $fixtureFileInfo
        );

        $this->assertSame(
            $inputFileInfoAndExpectedFileInfo->getExpectedFileInfo()->getContents(),
            $phpstanFileContent,
            $fixtureFileInfo->getRelativeFilePathFromCwd()
        );
    }

    public function provideDataForPHPStan(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.xml');
    }
}
