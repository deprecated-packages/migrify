<?php

declare(strict_types=1);

namespace Migrify\SnifferFixerToECS;

use Migrify\MigrifyKernel\Exception\ShouldNotHappenException;
use Migrify\PhpConfigPrinter\YamlToPhpConverter;
use Nette\Loaders\RobotLoader;
use Nette\Utils\Strings;
use PhpCsFixer\Config;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Migrify\SnifferFixerToECS\Tests\FixerToECSConverter\FixerToECSConverterTest
 */
final class FixerToECSConverter
{
    /**
     * @var mixed[]
     */
    private const SETS_PARAMETER = [];

    /**
     * @var mixed[]
     */
    private const SKIP_PARAMETER = [];

    /**
     * @var YamlToPhpConverter
     */
    private $yamlToPhpConverter;

    /**
     * @var SymfonyConfigFormatFactory
     */
    private $symfonyConfigFormatFactory;

    /**
     * @var string[]
     */
    private $fixerClasses = [];

    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;

    public function __construct(
        YamlToPhpConverter $yamlToPhpConverter,
        SymfonyConfigFormatFactory $symfonyConfigFormatFactory,
        PrivatesAccessor $privatesAccessor
    ) {
        $this->yamlToPhpConverter = $yamlToPhpConverter;
        $this->symfonyConfigFormatFactory = $symfonyConfigFormatFactory;
        $this->privatesAccessor = $privatesAccessor;
    }

    public function convertFile(SmartFileInfo $phpcsFileInfo): string
    {
        $config = include $phpcsFileInfo->getRealPath();
        if (! $config instanceof Config) {
            throw new ShouldNotHappenException();
        }

        $fixerClasses = $this->collectFixerClasses($config);

        $pathsParameter = $this->collectPathsParameter($config);
        $excludePathsParameter = $this->collectExcludePathsParameter($config);

        $yaml = $this->symfonyConfigFormatFactory->createSymfonyConfigFormat(
            $fixerClasses,
            self::SETS_PARAMETER,
            self::SKIP_PARAMETER,
            $excludePathsParameter,
            $pathsParameter
        );

        return $this->yamlToPhpConverter->convertYamlArray($yaml);
    }

    private function resolveSniffClassFromRuleName(string $ruleName): string
    {
        $fixerShortClassName = $this->resolveFixerShortClassName($ruleName);

        foreach ($this->getCoreFixerClasses() as $coreFixerClass) {
            if (Strings::endsWith($coreFixerClass, '\\' . $fixerShortClassName)) {
                return $coreFixerClass;
            }
        }

        $message = sprintf('Fixer class for "%s" rule was not found', $ruleName);
        throw new ShouldNotHappenException($message);
    }

    /**
     * @return string[]
     */
    private function getCoreFixerClasses(): array
    {
        if ($this->fixerClasses !== []) {
            return $this->fixerClasses;
        }

        $robotLoader = new RobotLoader();
        $robotLoader->addDirectory(__DIR__ . '/../../../vendor/friendsofphp/php-cs-fixer/src');

        $robotLoader->acceptFiles = ['*Fixer.php'];
        $robotLoader->rebuild();

        $this->fixerClasses = array_keys($robotLoader->getIndexedClasses());

        return $this->fixerClasses;
    }

    private function resolveFixerShortClassName(string $ruleName): string
    {
        $ruleClassParts = [];

        $ruleParts = explode('_', $ruleName);
        foreach ($ruleParts as $rulePart) {
            $ruleClassParts[] = ucfirst($rulePart);
        }

        $ruleClassParts[] = 'Fixer';

        return implode('', $ruleClassParts);
    }

    private function collectFixerClasses(Config $config): array
    {
        $fixerClasses = [];

        /** @var array<string, mixed[]|null> $rules */
        $rules = $config->getRules();
        foreach ($rules as $ruleName => $ruleConfiguration) {
            $sniffClass = $this->resolveSniffClassFromRuleName($ruleName);
            $fixerClasses[$sniffClass] = $ruleConfiguration !== null ? [
                'calls' => [['configure', [$ruleConfiguration]]],
            ] : null;
        }
        return $fixerClasses;
    }

    /**
     * @return string[]
     */
    private function collectPathsParameter(Config $config): array
    {
        $finder = $config->getFinder();

        return $this->privatesAccessor->getPrivateProperty($finder, 'dirs');
    }

    /**
     * @return string[]
     */
    private function collectExcludePathsParameter(Config $config): array
    {
        $finder = $config->getFinder();

        $excludePaths = $this->privatesAccessor->getPrivateProperty($finder, 'exclude');

        $normalizedExcludePaths = [];
        foreach ($excludePaths as $excludePath) {
            // drop default one, not to confuse users
            if ($excludePath === 'vendor') {
                continue;
            }

            $normalizedExcludePaths[] = $excludePath;
        }

        return $normalizedExcludePaths;
    }
}
