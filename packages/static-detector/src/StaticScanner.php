<?php

declare(strict_types=1);

namespace Migrify\StaticDetector;

use Migrify\StaticDetector\NodeVisitor\StaticCollectNodeVisitor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use Symplify\SmartFileSystem\SmartFileInfo;

final class StaticScanner
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var NodeTraverser
     */
    private $staticCollectNodeTraverser;

    public function __construct(StaticCollectNodeVisitor $staticCollectNodeVisitor, Parser $parser)
    {
        $this->parser = $parser;

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->addVisitor($staticCollectNodeVisitor);
        $this->staticCollectNodeTraverser = $nodeTraverser;
    }

    public function scanFileInfo(SmartFileInfo $smartFileInfo): void
    {
        $nodes = $this->parser->parse($smartFileInfo->getContents());
        if ($nodes === null) {
            return;
        }

        $this->staticCollectNodeTraverser->traverse($nodes);
    }
}
