<?php

declare(strict_types=1);

namespace Migrify\StaticDetector;

use Migrify\StaticDetector\CurrentProvider\CurrentFileInfoProvider;
use Migrify\StaticDetector\NodeTraverser\StaticCollectNodeTraverser;
use PhpParser\Parser;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Migrify\StaticDetector\Tests\StaticScanner\StaticScannerTest
 */
final class StaticScanner
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var StaticCollectNodeTraverser
     */
    private $staticCollectNodeTraverser;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var CurrentFileInfoProvider
     */
    private $currentFileInfoProvider;

    public function __construct(
        StaticCollectNodeTraverser $staticCollectNodeTraverser,
        Parser $parser,
        SymfonyStyle $symfonyStyle,
        CurrentFileInfoProvider $currentFileInfoProvider
    ) {
        $this->parser = $parser;
        $this->staticCollectNodeTraverser = $staticCollectNodeTraverser;
        $this->symfonyStyle = $symfonyStyle;
        $this->currentFileInfoProvider = $currentFileInfoProvider;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
    public function scanFileInfos(array $fileInfos): void
    {
        $this->symfonyStyle->note('Looking for static methods and their calls...');

        $stepCount = count($fileInfos);
        $this->symfonyStyle->progressStart($stepCount);

        foreach ($fileInfos as $fileInfo) {
            $this->currentFileInfoProvider->setCurrentFileInfo($fileInfo);

            $processingMessage = sprintf('Processing "%s" file', $fileInfo->getRelativeFilePathFromCwd());

            if ($this->symfonyStyle->isDebug()) {
                $this->symfonyStyle->note($processingMessage);
            } else {
                $this->symfonyStyle->progressAdvance();
            }

            // collect static calls
            // collect static class methods
            $this->scanFileInfo($fileInfo);
        }

        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->success('Scanning done');
        $this->symfonyStyle->newLine(1);
    }

    private function scanFileInfo(SmartFileInfo $smartFileInfo): void
    {
        $nodes = $this->parser->parse($smartFileInfo->getContents());
        if ($nodes === null) {
            return;
        }

        $this->staticCollectNodeTraverser->traverse($nodes);
    }
}
