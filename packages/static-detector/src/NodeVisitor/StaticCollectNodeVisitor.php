<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\NodeVisitor;

use Migrify\StaticDetector\Collector\StaticNodeCollector;
use Migrify\StaticDetector\Exception\ShouldNotHappenException;
use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;

final class StaticCollectNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var StaticNodeCollector
     */
    private $staticNodeCollector;

    /**
     * @var ClassLike|null
     */
    private $currentClassLike;

    public function __construct(StaticNodeCollector $staticNodeCollector)
    {
        $this->staticNodeCollector = $staticNodeCollector;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof ClassLike) {
            $this->currentClassLike = $node;
        }

        if ($node instanceof StaticCall) {
            $this->staticNodeCollector->addStaticCall($node, $this->currentClassLike);
        }

        if ($node instanceof ClassMethod) {
            if (! $node->isStatic()) {
                return null;
            }

            if ($this->currentClassLike === null) {
                throw new ShouldNotHappenException('Class not found for static call');
            }

            $this->staticNodeCollector->addStaticClassMethod($node, $this->currentClassLike);
        }

        return null;
    }
}
