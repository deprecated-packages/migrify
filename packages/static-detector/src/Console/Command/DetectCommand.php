<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\Console\Command;

use Migrify\StaticDetector\Collector\StaticNodeCollector;
use Migrify\StaticDetector\Output\StaticReportReporter;
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

    /**
     * @var StaticReportReporter
     */
    private $staticReportReporter;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        SmartFileSystem $smartFileSystem,
        Finder $finder,
        FinderSanitizer $finderSanitizer,
        StaticScanner $staticScanner,
        StaticNodeCollector $staticNodeCollector,
        StaticReportReporter $staticReportReporter
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
        $this->finder = $finder;
        $this->finderSanitizer = $finderSanitizer;
        $this->staticScanner = $staticScanner;
        $this->staticNodeCollector = $staticNodeCollector;

        parent::__construct();

        $this->staticReportReporter = $staticReportReporter;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(
            self::ARGUMENT_SOURCE,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more directories to detect static in'
        );
        $this->setDescription('Show what static method calls are called where');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $this->resolveSource($input);
        $fileInfos = $this->findPhpFilesInDirectories($source);
        $this->staticScanner->scanFileInfos($fileInfos);

        $this->symfonyStyle->title('Static Report');
        $staticReport = $this->staticNodeCollector->generateStaticReport();

        $this->staticReportReporter->reportStaticClassMethods($staticReport);
        $this->staticReportReporter->reportTotalNumbers($staticReport);

        return ShellCode::SUCCESS;
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findPhpFilesInDirectories(array $directories): array
    {
        $finder = $this->finder->files()
            ->in($directories)
            ->name('*.php');

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @return string[]
     */
    private function resolveSource(InputInterface $input): array
    {
        $source = (array) $input->getArgument(self::ARGUMENT_SOURCE);

        foreach ($source as $singleSource) {
            if (! $this->smartFileSystem->exists($singleSource)) {
                throw new FileNotFoundException($singleSource);
            }
        }

        return $source;
    }
}
