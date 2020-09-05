<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\Tests\StaticScanner;

use Migrify\StaticDetector\Collector\StaticNodeCollector;
use Migrify\StaticDetector\HttpKernel\StaticDetectorKernel;
use Migrify\StaticDetector\StaticScanner;
use Migrify\StaticDetector\ValueObject\StaticReport;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class StaticScannerTest extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernel(StaticDetectorKernel::class);
    }

    public function testStaticClassMethodDetection(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixture/StaticCallFile.php.inc');
        $staticReport = $this->createStaticReportFromFileInfo($fileInfo);

        $this->assertSame(1, $staticReport->getStaticClassMethodCount());

        $staticClassMethodWithStaticCalls = $staticReport->getStaticClassMethodsWithStaticCalls()[0];
        $this->assertCount(0, $staticClassMethodWithStaticCalls->getStaticCalls());
    }

    public function testFileLocationWithLine(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixture/StaticCallFile.php.inc');
        $staticReport = $this->createStaticReportFromFileInfo($fileInfo);

        $staticClassMethodWithStaticCalls = $staticReport->getStaticClassMethodsWithStaticCalls()[0];

        $this->assertStringMatchesFormat(
            '%s/StaticScanner/Fixture/StaticCallFile.php.inc:9',
            $staticClassMethodWithStaticCalls->getStaticCallFileLocationWithLine()
        );
    }

    public function testSelf(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixture/StaticSelfFile.php.inc');
        $staticReport = $this->createStaticReportFromFileInfo($fileInfo);

        $this->assertSame(1, $staticReport->getStaticClassMethodCount());
        $this->assertSame(1, $staticReport->getStaticCallsCount());
    }

    public function testParent(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixture/StaticParentFile.php.inc');
        $staticReport = $this->createStaticReportFromFileInfo($fileInfo);

        $this->assertSame(1, $staticReport->getStaticClassMethodCount());
        $this->assertSame(1, $staticReport->getStaticCallsCount());
    }

    private function createStaticReportFromFileInfo(SmartFileInfo $fileInfo): StaticReport
    {
        $staticScanner = self::$container->get(StaticScanner::class);
        $staticScanner->scanFileInfos([$fileInfo]);

        $staticNodeCollector = self::$container->get(StaticNodeCollector::class);
        return $staticNodeCollector->generateStaticReport();
    }
}
