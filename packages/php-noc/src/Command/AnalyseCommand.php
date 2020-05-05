<?php

declare(strict_types=1);

namespace Migrify\PhpNoc\Command;

use Migrify\PhpNoc\DataCollector\NodeCollector;
use Migrify\PhpNoc\Finder\PhpFileFinder;
use Migrify\PhpNoc\NodeCollectingNodeTraverser;
use Migrify\PhpNoc\ValueObject\Option;
use PhpParser\Parser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class AnalyseCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var PhpFileFinder
     */
    private $phpFileFinder;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var NodeCollectingNodeTraverser
     */
    private $nodeCollectingNodeTraverser;

    /**
     * @var NodeCollector
     */
    private $nodeCollector;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        PhpFileFinder $phpFileFinder,
        Parser $parser,
        NodeCollectingNodeTraverser $nodeCollectingNodeTraverser,
        NodeCollector $nodeCollector
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->phpFileFinder = $phpFileFinder;
        $this->parser = $parser;
        $this->nodeCollectingNodeTraverser = $nodeCollectingNodeTraverser;

        parent::__construct();
        $this->nodeCollector = $nodeCollector;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Analyse nodes of code in provided path');
        $this->addArgument(
            Option::SOURCE,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to source(s) to analyse'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (array) $input->getArgument(Option::SOURCE);

        // 1. find files
        $phpFileInfos = $this->phpFileFinder->find($source);

        // 2. collect nodes in files
        foreach ($phpFileInfos as $phpFileInfo) {
            $nodes = $this->parser->parse($phpFileInfo->getContents());
            if ($nodes === null) {
                continue;
            }

            $this->nodeCollectingNodeTraverser->collect($nodes);
        }

        // 3. report collected data

        $nodeCountByType = $this->nodeCollector->getNodeCountByType();
        foreach ($nodeCountByType as $nodeType => $count) {
            $this->symfonyStyle->writeln(sprintf('%s: %d', $nodeType, $count));
        }

        // size
        $this->symfonyStyle->newLine(2);
        $directoryCount = $this->phpFileFinder->getDirectoryCount($source);
        $this->symfonyStyle->writeln(sprintf('Directories: %d', $directoryCount));
        $this->symfonyStyle->writeln(sprintf('Files: %d', count($phpFileInfos)));

        $this->symfonyStyle->newLine(1);
        $this->symfonyStyle->writeln(sprintf('Node count (NOC): %d', $this->nodeCollector->getNodeCount()));
        $this->symfonyStyle->newLine(1);

        $this->symfonyStyle->success('OK');

        return ShellCode::SUCCESS;
    }
}
