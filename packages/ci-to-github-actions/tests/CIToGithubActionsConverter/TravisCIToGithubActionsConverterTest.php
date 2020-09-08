<?php

declare(strict_types=1);

namespace Migrify\CIToGithubActions\Tests\CIToGithubActionsConverter;

use Iterator;
use Migrify\CIToGithubActions\CIToGithubActionsConverter;
use Migrify\CIToGithubActions\HttpKernel\CIToGithubActionsKernel;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class TravisCIToGithubActionsConverterTest extends AbstractKernelTestCase
{
    /**
     * @var CIToGithubActionsConverter
     */
    private $ciToGithubActionsConverter;

    protected function setUp(): void
    {
        $this->bootKernel(CIToGithubActionsKernel::class);
        $this->ciToGithubActionsConverter = self::$container->get(CIToGithubActionsConverter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $convertedFileContent = $this->ciToGithubActionsConverter->convert(
            $inputAndExpectedFileInfo->getInputFileInfo()
        );

        $this->assertSame(
            $inputAndExpectedFileInfo->getExpectedFileInfo()->getContents(),
            $convertedFileContent,
            $fixtureFileInfo->getRelativeFilePathFromCwd()
        );
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture/TravisCI', '*.yml');
    }
}
