<?php

declare(strict_types=1);

namespace Migrify\NeonToYaml\Tests;

use Iterator;
use Migrify\NeonToYaml\ArrayParameterCollector;
use Migrify\NeonToYaml\HttpKernel\NeonToYamlKernel;
use Migrify\NeonToYaml\NeonToYamlConverter;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NeonToYamlConverterTest extends AbstractKernelTestCase
{
    /**
     * @var NeonToYamlConverter
     */
    private $neonToYamlConverter;

    /**
     * @var ArrayParameterCollector
     */
    private $arrayParameterCollector;

    protected function setUp(): void
    {
        $this->bootKernel(NeonToYamlKernel::class);

        $this->neonToYamlConverter = self::$container->get(NeonToYamlConverter::class);
        $this->arrayParameterCollector = self::$container->get(ArrayParameterCollector::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos($fixtureFileInfo);

        $this->arrayParameterCollector->collectFromFiles([$inputAndExpected->getInputFileInfo()]);

        $convertedFileContent = $this->neonToYamlConverter->convertFileInfo($inputAndExpected->getInputFileInfo());

        $this->assertSame(
            $inputAndExpected->getExpectedFileInfo()
                ->getContents(),
            $convertedFileContent,
            $fixtureFileInfo->getRelativeFilePathFromCwd()
        );
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.neon');
    }
}
