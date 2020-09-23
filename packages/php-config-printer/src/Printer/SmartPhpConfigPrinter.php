<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\Printer;

use Migrify\PhpConfigPrinter\NodeFactory\ContainerConfiguratorReturnClosureFactory;
use Migrify\PhpConfigPrinter\Reflection\ConstantNameFromValueResolver;

/**
 * @see \Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\SmartPhpConfigPrinterTest
 */
final class SmartPhpConfigPrinter
{
    /**
     * @var ContainerConfiguratorReturnClosureFactory
     */
    private $configuratorReturnClosureFactory;

    /**
     * @var PhpParserPhpConfigPrinter
     */
    private $phpParserPhpConfigPrinter;

    /**
     * @var ConstantNameFromValueResolver
     */
    private $constantNameFromValueResolver;

    public function __construct(
        ContainerConfiguratorReturnClosureFactory $configuratorReturnClosureFactory,
        PhpParserPhpConfigPrinter $phpParserPhpConfigPrinter,
        ConstantNameFromValueResolver $constantNameFromValueResolver
    ) {
        $this->configuratorReturnClosureFactory = $configuratorReturnClosureFactory;
        $this->phpParserPhpConfigPrinter = $phpParserPhpConfigPrinter;
        $this->constantNameFromValueResolver = $constantNameFromValueResolver;
    }

    /**
     * @param array<string, mixed[]|null> $configuredServices
     */
    public function printConfiguredServices(array $configuredServices): string
    {
        $servicesWithConfigureCalls = [];
        foreach ($configuredServices as $service => $configuration) {
            $servicesWithConfigureCalls[$service] = $this->createServiceConfiguration($configuration, $service);
        }

        $return = $this->configuratorReturnClosureFactory->createFromYamlArray(
            ['services' => $servicesWithConfigureCalls]
        );

        return $this->phpParserPhpConfigPrinter->prettyPrintFile([$return]);
    }

    /**
     * @param mixed[]|null $configuration
     */
    private function createServiceConfiguration($configuration, string $class): ?array
    {
        if ($configuration === null || $configuration === []) {
            return null;
        }

        $configuration = $this->replaceKeyValuesInConfigurationWithConstants($configuration, $class);

        return [
            'calls' => [['configure', [$configuration]]],
        ];
    }

    /**
     * @return mixed|mixed[]
     */
    private function replaceKeyValuesInConfigurationWithConstants($configuration, string $class)
    {
        if (! is_array($configuration)) {
            return $configuration;
        }

        foreach ($configuration as $key => $subValue) {
            $constantName = $this->constantNameFromValueResolver->resolveFromValueAndClass($key, $class);
            if ($constantName === null) {
                continue;
            }

            unset($configuration[$key]);

            $classConstantReference = $class . '::' . $constantName;
            $configuration[$classConstantReference] = $subValue;
        }

        return $configuration;
    }
}
