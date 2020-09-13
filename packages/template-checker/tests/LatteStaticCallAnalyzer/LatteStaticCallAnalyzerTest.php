<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker\Tests\LatteStaticCallAnalyzer;

use Migrify\TemplateChecker\HttpKernel\TemplateCheckerKernel;
use Migrify\TemplateChecker\LatteStaticCallAnalyzer;
use Migrify\TemplateChecker\ValueObject\ClassMethodName;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LatteStaticCallAnalyzerTest extends AbstractKernelTestCase
{
    /**
     * @var LatteStaticCallAnalyzer
     */
    private $latteStaticCallAnalyzer;

    protected function setUp(): void
    {
        self::bootKernel(TemplateCheckerKernel::class);
        $this->latteStaticCallAnalyzer = self::$container->get(LatteStaticCallAnalyzer::class);
    }

    public function test(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixture/some.latte');
        $classMethodNames = $this->latteStaticCallAnalyzer->analyzeFileInfos([$fileInfo]);

        $this->assertCount(1, $classMethodNames);

        $classMethodName = $classMethodNames[0];
        $this->assertInstanceOf(ClassMethodName::class, $classMethodName);

        $this->assertSame('Project\MailHelper::getUnsubscribeHash', $classMethodName->getClassMethodName());
    }
}
