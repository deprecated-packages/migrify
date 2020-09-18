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
     */
    public function generate(array $projectDirectories): string
    {
        $srcAndTestsDirectories = $this->srcTestsDirectoriesFinder->findSrcAndTestsDirectories($projectDirectories);
        if ($srcAndTestsDirectories === null) {
            return '';
        }

        $fileContent = '';

        $sonarOrganization = $this->parameterProvider->provideParameter(Option::SONAR_ORGANIZATION);
        $sonarProjectKey = $this->parameterProvider->provideParameter(Option::SONAR_PROJECT_KEY);

        if ($sonarOrganization !== '') {
            $fileContent = $this->appendKeyLine($fileContent, SonarConfigKey::ORGANIZATION, $sonarOrganization);
        }

        if ($sonarProjectKey !== '') {
            $fileContent = $this->appendKeyLine($fileContent, SonarConfigKey::PROJECT_KEY, $sonarProjectKey);
        }

        if ($srcAndTestsDirectories->getRelativePathSrcDirectories() !== []) {
            $line = implode(',', $srcAndTestsDirectories->getRelativePathSrcDirectories());
            $fileContent = $this->appendKeyLine($fileContent, SonarConfigKey::SOURCES, $line);
        }

        if ($srcAndTestsDirectories->getRelativePathTestsDirectories() !== []) {
            $line = implode(',', $srcAndTestsDirectories->getRelativePathTestsDirectories());
            $fileContent = $this->appendKeyLine($fileContent, SonarConfigKey::TESTS, $line);
        }

        return rtrim($fileContent) . PHP_EOL;
    }

    private function appendKeyLine(string $fileContent, string $key, string $line): string
    {
        $fileContent .= sprintf('%s=%s', $key, $line);
        $fileContent .= PHP_EOL . PHP_EOL;

        return $fileContent;
    }
}
