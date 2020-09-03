<?php

declare(strict_types=1);

namespace Migrify\SnifferFixerToECS;

use Migrify\PhpConfigPrinter\YamlToPhpConverter;
use SimpleXMLElement;
use Symplify\EasyCodingStandard\Configuration\Option;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Migrify\SnifferFixerToECS\Tests\SnifferToECSConverter\SnifferToECSConverterTest
 */
final class SnifferToECSConverter
{
    /**
     * @var string
     */
    private const REF = 'ref';

    /**
     * @var YamlToPhpConverter
     */
    private $yamlToPhpConverter;

    /**
     * @var SymfonyConfigFormatFactory
     */
    private $symfonyConfigFormatFactory;

    public function __construct(
        YamlToPhpConverter $yamlToPhpConverter,
        SymfonyConfigFormatFactory $symfonyConfigFormatFactory
    ) {
        $this->yamlToPhpConverter = $yamlToPhpConverter;
        $this->symfonyConfigFormatFactory = $symfonyConfigFormatFactory;
    }

    public function convertFile(SmartFileInfo $phpcsFileInfo): string
    {
        $simpleXml = new SimpleXMLElement($phpcsFileInfo->getContents());

        $excludePathsParameter = [];
        $setsParameter = [];

        foreach ($simpleXml->children() as $name => $child) {
            // skip option
            if ($name === 'exclude-pattern') {
                $excludePathsParameter[] = (string) $child;
                continue;
            }

            if (! isset($child[self::REF])) {
                continue;
            }

            $ruleId = (string) $child[self::REF];
            if ($ruleId === 'PSR2') {
                $setsParameter[] = 'PSR_2';
            }
        }

        $sniffClasses = $this->collectSniffClasses($simpleXml);
        $skipParameter = $this->collectSkipParameter($simpleXml);

        $yaml = $this->symfonyConfigFormatFactory->createSymfonyConfigFormat(
            $sniffClasses,
            $setsParameter,
            $skipParameter,
            $excludePathsParameter
        );

        return $this->yamlToPhpConverter->convertYamlArray($yaml);
    }

    private function resolveClassFromStringName(string $ruleId): string
    {
        $ruleIdParts = explode('.', $ruleId);

        $ruleNameParts = [$ruleIdParts[0], 'Sniffs', $ruleIdParts[1], $ruleIdParts[2] . 'Sniff'];

        $sniffClass = implode('\\', $ruleNameParts);
        if (class_exists($sniffClass)) {
            return $sniffClass;
        }

        $coreSniffClass = 'PHP_CodeSniffer\Standards\\' . $sniffClass;
        if (class_exists($coreSniffClass)) {
            return $coreSniffClass;
        }

        return $sniffClass;
    }

    /**
     * @return array<string, mixed>
     */
    private function collectSniffClasses(SimpleXMLElement $simpleXml): array
    {
        $sniffClasses = [];

        foreach ($simpleXml->children() as $child) {
            if (! isset($child[self::REF])) {
                continue;
            }

            $ruleId = (string) $child[self::REF];
            if (! $this->isRuleStringReference($ruleId)) {
                continue;
            }

            $sniffClass = $this->resolveClassFromStringName($ruleId);
            $sniffClasses[$sniffClass] = $this->resolveServiceConfiguration($child);
        }

        return $sniffClasses;
    }

    private function isRuleStringReference(string $ruleId): bool
    {
        return substr_count($ruleId, '.') === 2;
    }

    /**
     * @return array<string, string[]|null>
     */
    private function collectSkipParameter(SimpleXMLElement $simpleXml): array
    {
        $skipParameter = [];

        foreach ($simpleXml->children() as $name => $child) {
            if ($name === 'rule' && isset($child->exclude)) {
                $id = (string) $child->exclude['name'];
                $className = $this->resolveClassFromStringName($id);
                $skipParameter[$className] = null;
            }

            if (! isset($child[self::REF])) {
                continue;
            }

            $ruleId = (string) $child[self::REF];
            if (! $this->isRuleStringReference($ruleId)) {
                continue;
            }

            $className = $this->resolveClassFromStringName($ruleId);

            $excludePatterns = $this->resolveExcludedPatterns($child);
            if ($excludePatterns === []) {
                continue;
            }

            /** @var string[] $uniqueClassNames */
            $uniqueClassNames = array_unique($excludePatterns);
            $skipParameter[$className] = $uniqueClassNames;
        }

        return $skipParameter;
    }

    /**
     * @return string[]
     */
    private function resolveExcludedPatterns(SimpleXMLElement $child): array
    {
        $excludePatterns = [];
        foreach ($child->children() as $childKey => $childValue) {
            if ($childKey !== 'exclude-pattern') {
                continue;
            }

            $excludePatterns[] = (string) $childValue;
        }

        return $excludePatterns;
    }

    private function resolveServiceConfiguration(SimpleXMLElement $child): ?array
    {
        if (! isset($child->properties)) {
            return null;
        }

        $serviceConfiguration = [];
        foreach ($child->properties as $properties) {
            foreach ($properties as $property) {
                $name = (string) $property->attributes()['name'];
                $value = (string) $property->attributes()['value'];

                // retype
                if (strlen((string) (int) $value) === strlen($value)) {
                    $value = (int) $value;
                }

                $serviceConfiguration[$name] = $value;
            }
        }

        if ($serviceConfiguration === []) {
            return null;
        }

        return $serviceConfiguration;
    }
}
