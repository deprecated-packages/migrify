<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\NodeVisitor;

use Migrify\StaticDetector\CurrentProvider\CurrentFileInfoProvider;
use Migrify\StaticDetector\ValueObject\StaticDetectorAttributeKey;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

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
        $currentFileInfo = $this->currentFileInfoProvider->getSmartFileInfo();

        $fileLine = $currentFileInfo->getRelativeFilePathFromCwd() . ':' . $node->getStartLine();
        $node->setAttribute(StaticDetectorAttributeKey::FILE_LINE, $fileLine);

        return null;
    }
}
