<?php

declare(strict_types=1);

namespace Migrify\PHPUnitUpgrader\Tests\FileInfoDecorator\AssertContainsInfoDecorator;

use Iterator;
use Migrify\PHPUnitUpgrader\FileInfoDecorator\AssertContainsInfoDecorator;
use Migrify\PHPUnitUpgrader\HttpKernel\PHPUnitUpgraderKernel;
use Migrify\PHPUnitUpgrader\ValueObject\FilePathWithContent;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AssertContainsInfoDecoratorTest extends AbstractKernelTestCase
{
    /**
     * @var AssertContainsInfoDecorator
     */
    private $assertContainsInfoDecorator;

    protected function setUp(): void
    {
        $this->bootKernel(PHPUnitUpgraderKernel::class);
        $this->assertContainsInfoDecorator = self::$container->get(AssertContainsInfoDecorator::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpected($fixtureFileInfo);

        // for path testing purposes, as fixture file has temporary address so it can be overriden and included (@todo improve in symplify/smart-filesystem
        $filePathWithContent = new FilePathWithContent(
            $fixtureFileInfo->getRelativeFilePathFromCwd(),
            $inputAndExpected->getInputFileInfo()
                ->getContents()
        );

        $changedContent = $this->assertContainsInfoDecorator->decorate(
            $filePathWithContent,
            new SmartFileInfo(__DIR__ . '/Source/phpunit_error_report.txt')
        );

        $this->assertSame($inputAndExpected->getExpected(), $changedContent);
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.php.inc');
    }
}
