<?php

declare(strict_types=1);

namespace Migrify\EasyCI\Sonar;

use Migrify\EasyCI\Finder\SrcTestsDirectoriesFinder;
use Migrify\EasyCI\ValueObject\Option;
use Migrify\EasyCI\ValueObject\SonarConfigKey;
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

    public function __construct(
        SrcTestsDirectoriesFinder $srcTestsDirectoriesFinder,
        ParameterProvider $parameterProvider
    ) {
        $this->srcTestsDirectoriesFinder = $srcTestsDirectoriesFinder;
        $this->parameterProvider = $parameterProvider;
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

        $fileContent = '';

        $sonarOrganization = $this->parameterProvider->provideParameter(Option::SONAR_ORGANIZATION);
        if ($sonarOrganization !== '') {
            $fileContent = $this->appendKeyLine($fileContent, SonarConfigKey::ORGANIZATION, $sonarOrganization);
        }

        $sonarProjectKey = $this->parameterProvider->provideParameter(Option::SONAR_PROJECT_KEY);
        if ($sonarProjectKey !== '') {
            $fileContent = $this->appendKeyLine($fileContent, SonarConfigKey::PROJECT_KEY, $sonarProjectKey);
        }

        if ($srcAndTestsDirectories->getRelativePathSrcDirectories() !== []) {
            $fileContent = $this->appendKeyLineArray(
                $fileContent,
                SonarConfigKey::SOURCES,
                $srcAndTestsDirectories->getRelativePathSrcDirectories()
            );
        }

        if ($srcAndTestsDirectories->getRelativePathTestsDirectories() !== []) {
            $fileContent = $this->appendKeyLineArray(
                $fileContent,
                SonarConfigKey::TESTS,
                $srcAndTestsDirectories->getRelativePathTestsDirectories()
            );
        }

        foreach ($extraParameters as $key => $value) {
            $fileContent = $this->appendKeyLine($fileContent, $key, $value);
        }

        return rtrim($fileContent) . PHP_EOL;
    }

    private function appendKeyLine(string $fileContent, string $key, string $line): string
    {
        $fileContent .= sprintf('%s=%s', $key, $line);
        $fileContent .= PHP_EOL . PHP_EOL;

        return $fileContent;
    }

    /**
     * @param string[] $data
     */
    private function appendKeyLineArray(string $fileContent, string $key, array $data): string
    {
        $line = implode(',', $data);
        return $this->appendKeyLine($fileContent, $key, $line);
    }
}
