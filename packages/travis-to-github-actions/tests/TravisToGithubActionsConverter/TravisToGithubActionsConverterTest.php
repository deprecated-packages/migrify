<?php

declare(strict_types=1);

namespace Migrify\TravisToGithubActions\Tests\TravisToGithubActionsConverter;

use Iterator;
use Migrify\TravisToGithubActions\HttpKernel\TravisToGithubActionsKernel;
use Migrify\TravisToGithubActions\TravisToGithubActionsConverter;
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
        $this->bootKernel(TravisToGithubActionsKernel::class);
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
            $inputAndExpectedFileInfo->getExpectedFileInfo()
                ->getContents(),
            $convertedFileContent,
            $fixtureFileInfo->getRelativeFilePathFromCwd()
        );
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.yml');
    }
}
