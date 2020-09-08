<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\NodeVisitor;

use Migrify\StaticDetector\Collector\StaticNodeCollector;
use Migrify\StaticDetector\Exception\ShouldNotHappenException;
use Migrify\StaticDetector\Strings\StringsFilter;
use Migrify\StaticDetector\ValueObject\Option;
use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

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

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var StringsFilter
     */
    private $stringsFilter;

    public function __construct(
        StaticNodeCollector $staticNodeCollector,
        ParameterProvider $parameterProvider,
        StringsFilter $stringsFilter
    ) {
        $this->staticNodeCollector = $staticNodeCollector;
        $this->parameterProvider = $parameterProvider;
        $this->stringsFilter = $stringsFilter;
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

            // is filter match?
            $filterClasses = (array) $this->parameterProvider->provideParameter(Option::FILTER_CLASSES);
            $currentClassName = (string) $this->currentClassLike->namespacedName;
            if (! $this->stringsFilter->isMatchOrFnMatch($currentClassName, $filterClasses)) {
                return null;
            }

            $this->staticNodeCollector->addStaticClassMethod($node, $this->currentClassLike);
        }

        return null;
    }
}
