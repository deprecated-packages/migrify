<?php

declare(strict_types=1);

namespace Migrify\PhpNoc\DataCollector;

use Nette\Utils\Arrays;
use PhpParser\Node;

final class NodeCollector
{
    /**
     * @var Node[][]
     */
    private $nodesByType = [];

    public function addByType(string $type, Node $node): void
    {
        $this->nodesByType[$type][] = $node;
    }

    /**
     * @return int[]
     */
    public function getNodeCountByType(): array
    {
        $nodeCountByType = [];
        foreach ($this->nodesByType as $type => $nodes) {
            $nodeCountByType[$type] = count($nodes);
        }

        return $nodeCountByType;
    }

    public function getNodeCount(): int
    {
        $nodes = Arrays::flatten($this->nodesByType);
        return count($nodes);
    }
}
