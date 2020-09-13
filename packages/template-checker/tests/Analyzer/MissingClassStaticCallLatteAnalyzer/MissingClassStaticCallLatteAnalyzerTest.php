<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker\Tests\Analyzer\MissingClassStaticCallLatteAnalyzer;

use Iterator;
use Migrify\TemplateChecker\Analyzer\MissingClassStaticCallLatteAnalyzer;
use Migrify\TemplateChecker\HttpKernel\TemplateCheckerKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class MissingClassStaticCallLatteAnalyzerTest extends AbstractKernelTestCase
{
    /**
     * @var MissingClassStaticCallLatteAnalyzer
     */
    private $missingClassStaticCallLatteAnalyzer;

    protected function setUp(): void
    {
        self::bootKernel(TemplateCheckerKernel::class);
        $this->missingClassStaticCallLatteAnalyzer = self::$container->get(MissingClassStaticCallLatteAnalyzer::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $inputFileInfo, int $expectedErrorCount): void
    {
        $errorMessages = $this->missingClassStaticCallLatteAnalyzer->analyze([$inputFileInfo]);
        $this->assertCount($expectedErrorCount, $errorMessages);
    }

    public function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/missing_class_static_call.latte'), 4];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/existing_class_static_call.latte'), 0];

        yield [new SmartFileInfo(__DIR__ . '/Fixture/non_call.latte'), 0];
    }
}
