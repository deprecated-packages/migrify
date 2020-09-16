<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\CaseConverter;

use Migrify\MigrifyKernel\Exception\NotImplementedYetException;
use Migrify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Migrify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use Migrify\PhpConfigPrinter\Sorter\YamlArgumentSorter;
use Migrify\PhpConfigPrinter\ValueObject\VariableName;
use Migrify\PhpConfigPrinter\ValueObject\YamlKey;
use Nette\Utils\Strings;
use PhpParser\BuilderHelpers;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;

/**
 * Handles this part:
 *
 * imports: <---
 */
final class ImportCaseConverter implements CaseConverterInterface
{
    /**
     * @see https://regex101.com/r/hOTdIE/1
     * @var string
     */
    private const INPUT_SUFFIX_REGEX = '#\.(yml|yaml|xml)$#';

    /**
     * @var YamlArgumentSorter
     */
    private $yamlArgumentSorter;

    /**
     * @var CommonNodeFactory
     */
    private $commonNodeFactory;

    public function __construct(YamlArgumentSorter $yamlArgumentSorter, CommonNodeFactory $commonNodeFactory)
    {
        $this->yamlArgumentSorter = $yamlArgumentSorter;
        $this->commonNodeFactory = $commonNodeFactory;
    }

    public function getKey(): string
    {
        return YamlKey::IMPORTS;
    }

    public function match(string $rootKey, $key, $values): bool
    {
        return $rootKey === YamlKey::IMPORTS;
    }

    public function convertToMethodCall($key, $values): Expression
    {
        if (is_array($values)) {
            $arguments = $this->yamlArgumentSorter->sortArgumentsByKeyIfExists($values, [
                YamlKey::RESOURCE => '',
                'type' => null,
                YamlKey::IGNORE_ERRORS => false,
            ]);

            return $this->createImportMethodCall($arguments);
        }

        throw new NotImplementedYetException();
    }

    /**
     * @param mixed[] $arguments
     */
    private function createImportMethodCall(array $arguments): Expression
    {
        $containerConfiguratorVariable = new Variable(VariableName::CONTAINER_CONFIGURATOR);

        $args = $this->createArgs($arguments);
        $methodCall = new MethodCall($containerConfiguratorVariable, 'import', $args);

        return new Expression($methodCall);
    }

    /**
     * @param mixed[] $arguments
     * @return Arg[]
     */
    private function createArgs(array $arguments): array
    {
        $args = [];
        foreach ($arguments as $name => $value) {
            if ($this->shouldSkipDefaultValue($name, $value, $arguments)) {
                continue;
            }

            $expr = $this->resolveExpr($value);
            $args[] = new Arg($expr);
        }

        return $args;
    }

    private function shouldSkipDefaultValue(string $name, $value, array $arguments): bool
    {
        // skip default value for "ignore_errors"
        if ($name === YamlKey::IGNORE_ERRORS && $value === false) {
            return true;
        }

        // check if default value for "type"
        if ($name !== 'type') {
            return false;
        }

        if ($value !== null) {
            return false;
        }

        // follow by default value for "ignore_errors"
        return isset($arguments[YamlKey::IGNORE_ERRORS]) && $arguments[YamlKey::IGNORE_ERRORS] === false;
    }

    private function replaceImportedFileSuffix($value)
    {
        if (! is_string($value)) {
            return $value;
        }

        return Strings::replace($value, self::INPUT_SUFFIX_REGEX, '.php');
    }

    private function resolveExpr($value): Expr
    {
        if (is_bool($value) || in_array($value, ['annotations', 'directory', 'glob'], true)) {
            return BuilderHelpers::normalizeValue($value);
        }

        if ($value === 'not_found') {
            return new String_('not_found');
        }

        $value = $this->replaceImportedFileSuffix($value);
        return $this->commonNodeFactory->createAbsoluteDirExpr($value);
    }
}
