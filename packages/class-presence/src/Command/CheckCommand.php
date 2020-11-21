<?php

declare(strict_types=1);

namespace Migrify\ClassPresence\Command;

use Migrify\ClassPresence\Configuration\Suffixes;
use Migrify\ClassPresence\Regex\NonExistingClassConstantExtractor;
use Migrify\ClassPresence\Regex\NonExistingClassExtractor;
use Migrify\MigrifyKernel\Command\AbstractMigrifyCommand;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\Finder\SmartFinder;

final class CheckCommand extends AbstractMigrifyCommand
{
    /**
     * @var NonExistingClassExtractor
     */
    private $nonExistingClassExtractor;

    /**
     * @var NonExistingClassConstantExtractor
     */
    private $nonExistingClassConstantExtractor;

    /**
     * @var Suffixes
     */
    private $suffixes;

    public function __construct(
        SmartFinder $smartFinder,
        NonExistingClassExtractor $nonExistingClassExtractor,
        NonExistingClassConstantExtractor $nonExistingClassConstantExtractor,
        Suffixes $suffixes
    ) {
        $this->smartFinder = $smartFinder;
        $this->nonExistingClassExtractor = $nonExistingClassExtractor;
        $this->nonExistingClassConstantExtractor = $nonExistingClassConstantExtractor;
        $this->suffixes = $suffixes;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Check configs and template for existing classes and class constants');
        $this->addArgument(
            MigrifyOption::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to directories or files to check'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = sprintf('Checking "%s" suffixes', implode('", "', $this->suffixes->provide()));
        $this->symfonyStyle->note($message);

        /** @var string[] $sources */
        $sources = (array) $input->getArgument(MigrifyOption::SOURCES);
        $fileInfos = $this->smartFinder->find($sources, $this->suffixes->provideRegex());

        $message = sprintf('Found %d files', count($fileInfos));
        $this->symfonyStyle->note($message);

        $nonExistingClassesByFile = $this->nonExistingClassExtractor->extractFromFileInfos($fileInfos);
        if ($nonExistingClassesByFile === []) {
            $this->symfonyStyle->success('All classes exist');
        }

        $nonExistingClassConstantsByFile = $this->nonExistingClassConstantExtractor->extractFromFileInfos($fileInfos);
        if ($nonExistingClassConstantsByFile === []) {
            $this->symfonyStyle->success('All class constants exists');
        }

        if ($nonExistingClassConstantsByFile === [] && $nonExistingClassesByFile === []) {
            return ShellCode::SUCCESS;
        }

        return $this->reportNonExistingElements($nonExistingClassesByFile, $nonExistingClassConstantsByFile);
    }

    /**
     * @param string[][] $nonExistingClassesByFile
     * @param string[][] $nonExistingClassConstantsByFile
     */
    private function reportNonExistingElements(
        array $nonExistingClassesByFile,
        array $nonExistingClassConstantsByFile
    ): int {
        foreach ($nonExistingClassesByFile as $file => $nonExistingClasses) {
            if ($nonExistingClasses === []) {
                continue;
            }

            $message = sprintf('File "%s" contains non-existing classes', $file);
            $this->symfonyStyle->title($message);
            $this->symfonyStyle->listing($nonExistingClasses);
            $this->symfonyStyle->newLine();
        }

        foreach ($nonExistingClassConstantsByFile as $file => $nonExistingClassConstants) {
            if ($nonExistingClassConstants === []) {
                continue;
            }

            $message = sprintf('File "%s" contains non-existing class constants', $file);
            $this->symfonyStyle->title($message);
            $this->symfonyStyle->listing($nonExistingClassConstants);
            $this->symfonyStyle->newLine();
        }

        return ShellCode::ERROR;
    }
}
