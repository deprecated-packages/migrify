<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\Console\Command;

use Migrify\StaticDetector\Collector\StaticNodeCollector;
use Migrify\StaticDetector\StaticScanner;
use Migrify\StaticDetector\ValueObject\StaticReport;
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

        // report who is where
        $staticReport = $this->staticNodeCollector->generateStaticReport();

        $this->symfonyStyle->title('Static Report');

        $this->reportStaticClassMethods($staticReport);
        $this->reportTotalNumbers($staticReport);

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

    private function reportStaticClassMethods(StaticReport $staticReport): void
    {
        $i = 1;
        foreach ($staticReport->getStaticClassMethodsWithStaticCalls() as $staticClassMethodWithStaticCalls) {
            // report static call name

            $message = sprintf(
                '<options=bold>%d) %s</>',
                $i,
                $staticClassMethodWithStaticCalls->getStaticClassMethodName()
            );
            $this->symfonyStyle->writeln($message);

            // report file location
            $message = $staticClassMethodWithStaticCalls->getStaticCallFileLocationWithLine();
            $this->symfonyStyle->writeln($message);
            ++$i;

            // report usages

            if ($staticClassMethodWithStaticCalls->getStaticCalls() !== []) {
                $this->symfonyStyle->writeln('Static calls in the code:');

                $this->symfonyStyle->listing($staticClassMethodWithStaticCalls->getStaticCallsFilePathsWithLines());
            } else {
                $this->symfonyStyle->warning('No static calls in the code... maybe in templates?');
            }

            $this->symfonyStyle->newLine(2);
        }
    }

    private function reportTotalNumbers(StaticReport $staticReport): void
    {
        $this->symfonyStyle->title('Static Overview');

        if ($staticReport->getStaticClassMethodCount() === 0) {
            $this->symfonyStyle->success(
                'No static class methods and static calls found. Are you sure this tool is working? ;)'
            );
            return;
        }

        $message = sprintf('* %d static methods', $staticReport->getStaticClassMethodCount());
        $this->symfonyStyle->writeln($message);

        $message = sprintf('* %d static calls', $staticReport->getStaticCallsCount());
        $this->symfonyStyle->writeln($message);

        $this->symfonyStyle->newLine();
    }
}
