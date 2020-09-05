<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\NodeVisitor;

use Migrify\StaticDetector\CurrentProvider\CurrentFileInfoProvider;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Rector\NodeTypeResolver\Node\AttributeKey;

final class FilePathNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var CurrentFileInfoProvider
     */
    private $currentFileInfoProvider;

    public function __construct(CurrentFileInfoProvider $currentFileInfoProvider)
    {
        $this->currentFileInfoProvider = $currentFileInfoProvider;
    }

    public function enterNode(Node $node)
    {
        $node->setAttribute(AttributeKey::FILE_INFO, $this->currentFileInfoProvider->getSmartFileInfo());

        return null;
    }
}
