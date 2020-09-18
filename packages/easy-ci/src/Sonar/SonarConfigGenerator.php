<?php

declare(strict_types=1);

namespace Migrify\EasyCI\Sonar;

use Migrify\EasyCI\Finder\SrcTestsDirectoriesFinder;
use Migrify\EasyCI\Printer\SonarConfigDataPrinter;
use Migrify\EasyCI\ValueObject\Option;
use Migrify\EasyCI\ValueObject\SonarConfigKey;
use Migrify\EasyCI\ValueObject\SrcAndTestsDirectories;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

/**
 * @see \Migrify\EasyCI\Tests\Sonar\SonarConfigGenerator\SonarConfigGeneratorTest
 */
final class SonarConfigGenerator
{
    /**
     * @var SrcTestsDirectoriesFinder
     */
    private $srcTestsDirectoriesFinder;

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var SonarConfigDataPrinter
     */
    private $sonarConfigDataPrinter;

    public function __construct(
        SrcTestsDirectoriesFinder $srcTestsDirectoriesFinder,
        ParameterProvider $parameterProvider,
        SonarConfigDataPrinter $sonarConfigDataPrinter
    ) {
        $this->srcTestsDirectoriesFinder = $srcTestsDirectoriesFinder;
        $this->parameterProvider = $parameterProvider;
        $this->sonarConfigDataPrinter = $sonarConfigDataPrinter;
    }

    /**
     * @param string[] $projectDirectories
     * @param array<string, mixed|mixed[]> $extraParameters
     */
    public function generate(array $projectDirectories, array $extraParameters): string
    {
        $srcAndTestsDirectories = $this->srcTestsDirectoriesFinder->findSrcAndTestsDirectories($projectDirectories);
        if ($srcAndTestsDirectories === null) {
            return '';
        }

        $sonarFileData = $this->createSonarFileData($srcAndTestsDirectories, $extraParameters);
        return $this->sonarConfigDataPrinter->print($sonarFileData);
    }

    /**
     * @param array<string, mixed|mixed[]> $extraParameters
     */
    private function createSonarFileData(SrcAndTestsDirectories $srcAndTestsDirectories, array $extraParameters): array
    {
        $sonarData = [
            SonarConfigKey::ORGANIZATION => $this->parameterProvider->provideParameter(Option::SONAR_ORGANIZATION),
            SonarConfigKey::PROJECT_KEY => $this->parameterProvider->provideParameter(Option::SONAR_PROJECT_KEY),
            SonarConfigKey::SOURCES => $srcAndTestsDirectories->getRelativePathSrcDirectories(),
            SonarConfigKey::TESTS => $srcAndTestsDirectories->getRelativePathTestsDirectories(),
        ];

        foreach ($extraParameters as $key => $value) {
            $sonarData[$key] = $value;
        }

        return $sonarData;
    }
}
