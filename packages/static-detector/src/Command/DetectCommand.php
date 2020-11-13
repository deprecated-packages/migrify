<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\Command;

use Migrify\MigrifyKernel\Command\AbstractMigrifyCommand;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Migrify\StaticDetector\Collector\StaticNodeCollector;
use Migrify\StaticDetector\Output\StaticReportReporter;
use Migrify\StaticDetector\StaticScanner;
use Migrify\StaticDetector\ValueObject\Option;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class DetectCommand extends AbstractMigrifyCommand
{
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

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    public function __construct(
        Finder $finder,
        FinderSanitizer $finderSanitizer,
        StaticScanner $staticScanner,
        StaticNodeCollector $staticNodeCollector,
        StaticReportReporter $staticReportReporter,
        ParameterProvider $parameterProvider
    ) {
        $this->finder = $finder;
        $this->finderSanitizer = $finderSanitizer;
        $this->staticScanner = $staticScanner;
        $this->staticNodeCollector = $staticNodeCollector;
        $this->staticReportReporter = $staticReportReporter;
        $this->parameterProvider = $parameterProvider;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            MigrifyOption::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more directories to detect static in'
        );
        $this->setDescription('Show what static method calls are called where');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $this->resolveSource($input);
        $fileInfos = $this->findPhpFilesInDirectories($source);

        $filterClasses = (array) $this->parameterProvider->provideParameter(Option::FILTER_CLASSES);
        foreach ($filterClasses as $filterClass) {
            $message = sprintf('Filtering only "%s" classes', $filterClass);
            $this->symfonyStyle->note($message);
        }

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
        $source = (array) $input->getArgument(MigrifyOption::SOURCES);

        foreach ($source as $singleSource) {
            if (! $this->smartFileSystem->exists($singleSource)) {
                $message = sprintf('Path "%s" was not found', $singleSource);
                throw new FileNotFoundException($message);
            }
        }

        return $source;
    }
}
