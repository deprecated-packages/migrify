<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker\Tests\StaticCallWithFilterReplacer;

use Iterator;
use Migrify\TemplateChecker\HttpKernel\TemplateCheckerKernel;
use Migrify\TemplateChecker\StaticCallWithFilterReplacer;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class StaticCallWithFilterReplacerTest extends AbstractKernelTestCase
{
    /**
     * @var StaticCallWithFilterReplacer
     */
    private $staticCallWithFilterReplacer;

    protected function setUp(): void
    {
        self::bootKernel(TemplateCheckerKernel::class);
        $this->staticCallWithFilterReplacer = self::$container->get(StaticCallWithFilterReplacer::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputFileInfoAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $inputFileInfo = $inputFileInfoAndExpectedFileInfo->getInputFileInfo();
        $changedContent = $this->staticCallWithFilterReplacer->processFileInfo($inputFileInfo);

        $expectedFileInfo = $inputFileInfoAndExpectedFileInfo->getExpectedFileInfo();
        $this->assertStringEqualsFile($expectedFileInfo->getPathname(), $changedContent);
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.latte');
    }
}
