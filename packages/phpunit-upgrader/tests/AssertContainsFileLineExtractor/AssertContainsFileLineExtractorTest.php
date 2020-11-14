<?php

declare(strict_types=1);

namespace Migrify\PHPUnitUpgrader\Tests\AssertContainsFileLineExtractor;

use Migrify\PHPUnitUpgrader\AssertContainsFileLineExtractor;
use Migrify\PHPUnitUpgrader\HttpKernel\PHPUnitUpgraderKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AssertContainsFileLineExtractorTest extends AbstractKernelTestCase
{
    /**
     * @var AssertContainsFileLineExtractor
     */
    private $assertContainsFileLineExtractor;

    protected function setUp(): void
    {
        $this->bootKernel(PHPUnitUpgraderKernel::class);
        $this->assertContainsFileLineExtractor = self::$container->get(AssertContainsFileLineExtractor::class);
    }

    public function test(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixture/phpunit_error_report.txt');

        $fileLines = $this->assertContainsFileLineExtractor->extract($fileInfo);
        $this->assertCount(1, $fileLines);

        $fileLine = $fileLines[0];
        $this->assertSame(99, $fileLine->getLine());
        $this->assertSame('somePath.php', $fileLine->getFilePath());
    }
}
