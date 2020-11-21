<?php

declare(strict_types=1);

namespace Migrify\PHPUnitUpgrader\Tests\FileInfoDecorator\SetUpTearDownVoidFileInfoDecorator;

use Iterator;
use Migrify\PHPUnitUpgrader\FileInfoDecorator\SetUpTearDownVoidFileInfoDecorator;
use Migrify\PHPUnitUpgrader\HttpKernel\PHPUnitUpgraderKernel;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SetUpTearDownVoidFileInfoDecoratorTest extends AbstractKernelTestCase
{
    /**
     * @var SetUpTearDownVoidFileInfoDecorator
     */
    private $setUpTearDownVoidFileInfoDecorator;

    protected function setUp(): void
    {
        $this->bootKernel(PHPUnitUpgraderKernel::class);
        $this->setUpTearDownVoidFileInfoDecorator = self::$container->get(SetUpTearDownVoidFileInfoDecorator::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpected($fixtureFileInfo);
        $changedContent = $this->setUpTearDownVoidFileInfoDecorator->decorate($inputAndExpected->getInputFileInfo());

        $this->assertSame($inputAndExpected->getExpected(), $changedContent);
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.php.inc');
    }
}
