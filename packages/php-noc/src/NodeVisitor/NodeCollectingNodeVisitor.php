<?php

declare(strict_types=1);

namespace Migrify\PhpNoc\NodeVisitor;

use Migrify\PhpNoc\DataCollector\NodeCollector;
use PhpParser\Node;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitorAbstract;

final class NodeCollectingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var NodeCollector
     */
    private $nodeCollector;

    public function __construct(NodeCollector $nodeCollector)
    {
        $this->nodeCollector = $nodeCollector;
    }

    public function enterNode(Node $node): ?Node
    {
        if ($node instanceof Expression) {
            return null;
        }

        $nodeClass = get_class($node);
        $this->nodeCollector->addByType($nodeClass, $node);

        return null;
    }
}
