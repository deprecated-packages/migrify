<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\NodeFactory;

use Migrify\ConfigTransformer\FormatSwitcher\Contract\RoutingCaseConverterInterface;
use Migrify\PhpConfigPrinter\PhpParser\NodeFactory\ConfiguratorClosureNodeFactory;
use PhpParser\Node;
use PhpParser\Node\Stmt\Return_;

final class RoutingConfiguratorReturnClosureFactory
{
    /**
     * @var ConfiguratorClosureNodeFactory
     */
    private $containerConfiguratorClosureNodeFactory;

    /**
     * @var RoutingCaseConverterInterface[]
     */
    private $routingCaseConverters = [];

    /**
     * @param RoutingCaseConverterInterface[] $routingCaseConverters
     */
    public function __construct(
        ConfiguratorClosureNodeFactory $containerConfiguratorClosureNodeFactory,
        array $routingCaseConverters = []
    ) {
        $this->containerConfiguratorClosureNodeFactory = $containerConfiguratorClosureNodeFactory;
        $this->routingCaseConverters = $routingCaseConverters;
    }

    public function createFromArrayData(array $arrayData): Return_
    {
        $stmts = $this->createClosureStmts($arrayData);
        $closure = $this->containerConfiguratorClosureNodeFactory->createRoutingClosureFromStmts($stmts);
        return new Return_($closure);
    }

    /**
     * @return Node[]
     */
    private function createClosureStmts(array $arrayData): array
    {
        $arrayData = $this->removeEmptyValues($arrayData);
        return $this->createNodesFromCaseConverters($arrayData);
    }

    private function removeEmptyValues(array $yamlData): array
    {
        return array_filter($yamlData);
    }

    /**
     * @param mixed[] $arrayData
     * @return Node[]
     */
    private function createNodesFromCaseConverters(array $arrayData): array
    {
        $nodes = [];

        foreach ($arrayData as $key => $values) {
            $expression = null;

            foreach ($this->routingCaseConverters as $caseConverter) {
                if (! $caseConverter->match($key, $values)) {
                    continue;
                }

                $expression = $caseConverter->convertToMethodCall($key, $values);
                break;
            }

            if ($expression === null) {
                continue;
            }

            $nodes[] = $expression;
        }

        return $nodes;
    }
}
