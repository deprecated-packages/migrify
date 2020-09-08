<?php

declare(strict_types=1);

namespace Migrify\PHPMDDecomposer\PHPMDDecomposer;

use DOMDocument;
use DOMElement;
use Migrify\PHPMDDecomposer\Blueprint\PHPMDToPHPStanBluerprint;
use Migrify\PHPMDDecomposer\ValueObject\Config\PHPStanConfig;
use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Migrify\PHPMDDecomposer\Tests\PHPMDDecomposer\PHPStanPHPMDDecomposer\PHPStanPHPMDDecomposerTest
 */
final class PHPStanPHPMDDecomposer extends AbstractPHPMDDecomposer
{
    /**
     * @var string
     */
    private const PHPMD_KEY_EXCLUDE_PATTERN = 'exclude-pattern';

    /**
     * @var string
     */
    private const PHPSTAN_KEY_EXCLUDES_ANALYSE = 'excludes_analyse';

    /**
     * @var string
     * @see https://regex101.com/r/ZjOPUX/1/
     */
    private const SPLIT_BY_COMMA_REGEX = '#\,[\s+]?#';

    /**
     * @var string
     */
    private const VALUE_PLACEHOLDER = '%value%';

    /**
     * @var PHPMDToPHPStanBluerprint
     */
    private $phpMDToPHPStanBluerprint;

    public function __construct(PHPMDToPHPStanBluerprint $phpmdToPHPStanBluerprint)
    {
        $this->phpMDToPHPStanBluerprint = $phpmdToPHPStanBluerprint;
    }

    public function decompose(SmartFileInfo $phpmdFileInfo): PHPStanConfig
    {
        $domDocument = $this->createDOMDocumentFromXmlFileInfo($phpmdFileInfo);

        $phpStanConfig = new PHPStanConfig();

        $this->decorateWithExcludedPaths($domDocument, $phpStanConfig);
        $this->decorateWithRules($domDocument, $phpStanConfig);

        return $phpStanConfig;
    }

    private function decorateWithExcludedPaths(DOMDocument $domDocument, PHPStanConfig $phpStanConfig): void
    {
        foreach ($domDocument->getElementsByTagName(self::PHPMD_KEY_EXCLUDE_PATTERN) as $excludePattern) {
            $currentPHPStanConfig = new PHPStanConfig([], [
                self::PHPSTAN_KEY_EXCLUDES_ANALYSE => [$excludePattern->nodeValue],
            ]);

            /** @var DOMElement $excludePattern */
            $phpStanConfig->merge($currentPHPStanConfig);
        }
    }

    private function decorateWithRules(DOMDocument $domDocument, PHPStanConfig $phpStanConfig): void
    {
        foreach ($domDocument->getElementsByTagName('rule') as $rule) {
            foreach ($this->phpMDToPHPStanBluerprint->provide() as $matchToConfig) {
                /** @var DOMElement $rule */
                if (! $matchToConfig->isMatch($rule)) {
                    continue;
                }

                $this->mergeWithMatchingParameters($matchToConfig->getConfig(), $rule, $phpStanConfig);

                // covered by PHPStan native
                $phpStanConfig->merge($matchToConfig->getConfig());

                // this rule is matched, jump to another one
                continue 2;
            }
        }
    }

    /**
     * @param mixed[] $parameterArray
     * @param mixed $resolvedValue
     * @return mixed[]
     */
    private function replaceValuePlaceholderWithValue(array $parameterArray, $resolvedValue): array
    {
        foreach ($parameterArray as $key => $value) {
            if ($value === self::VALUE_PLACEHOLDER) {
                $parameterArray[$key] = $resolvedValue;
            }

            if (is_array($value)) {
                $parameterArray[$key] = $this->replaceValuePlaceholderWithValue($value, $resolvedValue);
            }
        }

        return $parameterArray;
    }

    private function mergeWithMatchingParameters(
        PHPStanConfig $phpmdToPHPStanConfig,
        DOMElement $domElement,
        PHPStanConfig $phpStanConfig
    ): void {
        foreach ($phpmdToPHPStanConfig->getMatchingParameters() as $phpMDParameterName => $phpStanParameterName) {
            foreach ($domElement->getElementsByTagName('property') as $property) {
                /** @var DOMElement $property */
                if ($property->getAttribute('name') !== $phpMDParameterName) {
                    continue;
                }

                $resolvedValue = $property->getAttribute('value');

                if (Strings::match($resolvedValue, self::SPLIT_BY_COMMA_REGEX)) {
                    $resolvedValue = Strings::split($resolvedValue, self::SPLIT_BY_COMMA_REGEX);
                    $resolvedValue = array_values($resolvedValue);
                } elseif (Strings::length((string) (int) $resolvedValue) === Strings::length($resolvedValue)) {
                    // is numeric value
                    $resolvedValue = (int) $resolvedValue;
                }

                // replace %value% with current value
                $phpstanParameters = $this->replaceValuePlaceholderWithValue($phpStanParameterName, $resolvedValue);

                $currentParametersPHPStanConfig = new PHPStanConfig([], $phpstanParameters);

                // covered by PHPStan native
                $phpStanConfig->merge($currentParametersPHPStanConfig);
                break;
            }
        }
    }
}
