<?php

declare(strict_types=1);

namespace Migrify\CIToGithubActions\Tests\CIToGithubActionsConverter\TravisToGithubActionsConverter;

use Iterator;
use Migrify\CIToGithubActions\CIToGithubActionsConverter\TravisToGithubActionsConverter;
use Migrify\CIToGithubActions\HttpKernel\CIToGithubActionsKernel;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class TravisToGithubActionsConverterTest extends AbstractKernelTestCase
{
    /**
     * @var TravisToGithubActionsConverter
     */
    private $travisToGithubActionsConverter;

    protected function setUp(): void
    {
        $this->bootKernel(CIToGithubActionsKernel::class);
        $this->travisToGithubActionsConverter = self::$container->get(TravisToGithubActionsConverter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $convertedFileContent = $this->travisToGithubActionsConverter->convert(
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
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.yml');
    }
}
