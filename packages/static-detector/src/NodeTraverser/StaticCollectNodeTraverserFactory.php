<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\NodeTraverser;

use Migrify\StaticDetector\NodeVisitor\FilePathNodeVisitor;
use Migrify\StaticDetector\NodeVisitor\StaticCollectNodeVisitor;
use PhpParser\NodeVisitor\NameResolver;

final class StaticCollectNodeTraverserFactory
{
    /**
     * @var StaticCollectNodeVisitor
     */
    private $staticCollectNodeVisitor;

    /**
     * @var FilePathNodeVisitor
     */
    private $filePathNodeVisitor;

    public function __construct(
        StaticCollectNodeVisitor $staticCollectNodeVisitor,
        FilePathNodeVisitor $filePathNodeVisitor
    ) {
        $this->staticCollectNodeVisitor = $staticCollectNodeVisitor;
        $this->filePathNodeVisitor = $filePathNodeVisitor;
    }

    public function create(): StaticCollectNodeTraverser
    {
        $staticCollectNodeTraverser = new StaticCollectNodeTraverser();
        $staticCollectNodeTraverser->addVisitor(new NameResolver());
        $staticCollectNodeTraverser->addVisitor($this->staticCollectNodeVisitor);
        $staticCollectNodeTraverser->addVisitor($this->filePathNodeVisitor);

        return $staticCollectNodeTraverser;
    }
}
