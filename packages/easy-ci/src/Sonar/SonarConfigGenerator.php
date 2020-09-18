<?php

declare(strict_types=1);

namespace Migrify\EasyCI\Sonar;

use Migrify\EasyCI\Finder\SrcTestsDirectoriesFinder;
use Migrify\EasyCI\ValueObject\Option;
use Migrify\EasyCI\ValueObject\SonarConfigKey;
use Nette\Utils\Strings;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Migrify\EasyCI\Tests\Sonar\SonarConfigGenerator\SonarConfigGeneratorTest
 */
final class SonarConfigGenerator
{
    /**
     * @see https://regex101.com/r/YbxRKD/1
     * @var string
     */
    private const SONAR_KEY_VALUE_REGEX = '#(?<key>sonar\..*?)=(?<value>.*?)$#m';

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
    public function generateWithOriginalFile(array $projectDirectories, SmartFileInfo $originalFileInfo): string
    {
        $fileContent = $this->generate($projectDirectories);

        // to keep distance
        $fileContent .= PHP_EOL;

        $sonarKeyValues = $this->resolveSonarKeyValues($originalFileInfo);
        foreach ($sonarKeyValues as $key => $value) {
            // prevent overriding generated keys
            $keyPattern = '#^' . preg_quote($key, '#') . '=(.*?)$#ms';
            if (Strings::match($fileContent, $keyPattern)) {
                continue;
            }

            $fileContent = $this->appendKeyLine($fileContent, $key, $value);
        }

        return rtrim($fileContent) . PHP_EOL;
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

    /**
     * @return array<string, string>
     */
    private function resolveSonarKeyValues(SmartFileInfo $originalFileInfo): array
    {
        $sonarKeyValueMatches = Strings::matchAll($originalFileInfo->getContents(), self::SONAR_KEY_VALUE_REGEX);

        $originaSonarKeyValues = [];

        foreach ($sonarKeyValueMatches as $sonarKeyValueMatch) {
            $key = (string) $sonarKeyValueMatch['key'];
            $value = (string) $sonarKeyValueMatch['value'];

            $originaSonarKeyValues[$key] = $value;
        }

        return $originaSonarKeyValues;
    }
}
