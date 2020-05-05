<?php

declare(strict_types=1);

namespace Migrify\PhpNoc;

use Migrify\PhpNoc\NodeVisitor\NodeCollectingNodeVisitor;
use PhpParser\Node;
use PhpParser\NodeTraverser;

final class NodeCollectingNodeTraverser
{
    /**
     * @var NodeTraverser
     */
    private $nodeTraverser;

    public function __construct(NodeCollectingNodeVisitor $nodeCollectingNodeVisitor)
    {
        $this->nodeTraverser = new NodeTraverser();
        $this->nodeTraverser->addVisitor($nodeCollectingNodeVisitor);
    }

    /**
     * @param Node[] $nodes
     */
    public function collect(array $nodes): void
    {
        $this->nodeTraverser->traverse($nodes);
    }
}
