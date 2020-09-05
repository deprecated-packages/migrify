<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\Console\Command;

use Migrify\StaticDetector\Collector\StaticNodeCollector;
use Migrify\StaticDetector\StaticScanner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class DetectCommand extends Command
{
    /**
     * @var string
     */
    private const ARGUMENT_SOURCE = 'source';

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    /**
     * @var StaticScanner
     */
    private $staticScanner;

    /**
     * @var StaticNodeCollector
     */
    private $staticNodeCollector;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        SmartFileSystem $smartFileSystem,
        Finder $finder,
        FinderSanitizer $finderSanitizer,
        StaticScanner $staticScanner,
        StaticNodeCollector $staticNodeCollector
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
        $this->finder = $finder;
        $this->finderSanitizer = $finderSanitizer;
        $this->staticScanner = $staticScanner;
        $this->staticNodeCollector = $staticNodeCollector;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(self::ARGUMENT_SOURCE, InputArgument::REQUIRED, 'Directory to detect static in');
        $this->setDescription('Show what static method calls are called where');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $this->resolveSource($input);
        $fileInfos = $this->findPhpFilesInDirectory($source);
        $this->scanFileInfos($fileInfos);

        // report who is where
        $staticReport = $this->staticNodeCollector->generateStaticReport();

        $this->symfonyStyle->title('Static Report');
        if ($staticReport->getStaticClassMethodCount() === 0) {
            $this->symfonyStyle->success(
                'No static class methods and static calls found. Are you sure this tool is working? ;)'
            );
        } else {
            $message = sprintf('* %d static methods', $staticReport->getStaticClassMethodCount());
            $this->symfonyStyle->writeln($message);

            $this->symfonyStyle->newLine(1);

            $message = sprintf('* %d static calls', $staticReport->getStaticCallsCount());
            $this->symfonyStyle->writeln($message);

            $this->symfonyStyle->newLine(2);
        }

        return ShellCode::SUCCESS;
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findPhpFilesInDirectory(string $source): array
    {
        $finder = $this->finder->files()
            ->in($source)
            ->name('*.php');

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
    private function scanFileInfos(array $fileInfos): void
    {
        $this->symfonyStyle->note('Looking for static methods and their calls...');

        $stepCount = count($fileInfos);
        $this->symfonyStyle->progressStart($stepCount);

        foreach ($fileInfos as $fileInfo) {
            $processingMessage = sprintf('Processing "%s" file', $fileInfo->getRelativeFilePathFromCwd());

            if ($this->symfonyStyle->isDebug()) {
                $this->symfonyStyle->note($processingMessage);
            } else {
                $this->symfonyStyle->progressAdvance();
            }

            // collect static calls
            // collect static class methods
            $this->staticScanner->scanFileInfo($fileInfo);
        }

        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->success('Scanning done');
        $this->symfonyStyle->newLine(1);
    }

    private function resolveSource(InputInterface $input): string
    {
        $source = (string) $input->getArgument(self::ARGUMENT_SOURCE);
        if (! $this->smartFileSystem->exists($source)) {
            throw new FileNotFoundException($source);
        }

        return $source;
    }
}
