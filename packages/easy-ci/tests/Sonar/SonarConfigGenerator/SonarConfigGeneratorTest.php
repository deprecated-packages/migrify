<?php

declare(strict_types=1);

namespace Migrify\EasyCI\Tests\Sonar\SonarConfigGenerator;

use Iterator;
use Migrify\EasyCI\HttpKernel\EasyCIKernel;
use Migrify\EasyCI\Sonar\SonarConfigGenerator;
use Migrify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SonarConfigGeneratorTest extends AbstractKernelTestCase
{
    /**
     * @var SonarConfigGenerator
     */
    private $sonarConfigGenerator;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->sonarConfigGenerator = self::$container->get(SonarConfigGenerator::class);

        /** @var ParameterProvider $parameterProvider */
        $parameterProvider = self::$container->get(ParameterProvider::class);
        $parameterProvider->changeParameter(Option::SONAR_ORGANIZATION, 'some_organization');
        $parameterProvider->changeParameter(Option::SONAR_PROJECT_KEY, 'some_project');
    }

    public function test(): void
    {
        $sonarConfigContent = $this->sonarConfigGenerator->generate([__DIR__ . '/Fixture']);
        $this->assertStringEqualsFile(__DIR__ . '/Fixture/expected_config.txt', $sonarConfigContent);
    }

    /**
     * @dataProvider provideData()
     */
    public function testWithOriginalFile(
        string $fixtureDirectory,
        string $originalFilePath,
        string $expectedSonartConfig
    ): void {
        $originalFileInfo = new SmartFileInfo($originalFilePath);

        $sonarConfigContent = $this->sonarConfigGenerator->generateWithOriginalFile(
            [$fixtureDirectory],
            $originalFileInfo
        );

        $this->assertStringEqualsFile($expectedSonartConfig, $sonarConfigContent);
    }

    public function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Fixture',
            __DIR__ . '/Fixture/original_config.txt',
            __DIR__ . '/Fixture/expected_modified_original_config.txt',
        ];

        yield [
            __DIR__ . '/Fixture',
            __DIR__ . '/Fixture/original_config_with_paths.txt',
            __DIR__ . '/Fixture/expected_modified_original_config.txt',
        ];
    }
}
