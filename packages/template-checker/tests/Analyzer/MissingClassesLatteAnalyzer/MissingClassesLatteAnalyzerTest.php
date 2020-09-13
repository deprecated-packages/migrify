<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker\Tests\Analyzer\MissingClassesLatteAnalyzer;

use Iterator;
use Migrify\TemplateChecker\Analyzer\MissingClassesLatteAnalyzer;
use Migrify\TemplateChecker\HttpKernel\TemplateCheckerKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class MissingClassesLatteAnalyzerTest extends AbstractKernelTestCase
{
    /**
     * @var MissingClassesLatteAnalyzer
     */
    private $missingClassesLatteAnalyzer;

    protected function setUp(): void
    {
        self::bootKernel(TemplateCheckerKernel::class);
        $this->missingClassesLatteAnalyzer = self::$container->get(MissingClassesLatteAnalyzer::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $inputFileInfo, int $expectedErrorCount): void
    {
        $result = $this->missingClassesLatteAnalyzer->analyze([$inputFileInfo]);
        $this->assertCount($expectedErrorCount, $result);
    }

    public function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/missing_classes.latte'), 1];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/existing_classes.latte'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/non_classes.latte'), 0];
    }
}
