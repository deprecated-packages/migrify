<?php

declare(strict_types=1);

namespace Migrify\ClassPresence\Command;

use Migrify\ClassPresence\Finder\FileFinder;
use Migrify\ClassPresence\Regex\NonExistingClassConstantExtractor;
use Migrify\ClassPresence\Regex\NonExistingClassExtractor;
use Migrify\ClassPresence\ValueObject\StaticCheckedFileSuffix;
use Migrify\MigrifyKernel\Command\AbstractMigrifyCommand;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\ShellCode;

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
     * @var FileFinder
     */
    private $fileFinder;

    public function __construct(
        FileFinder $fileFinder,
        NonExistingClassExtractor $nonExistingClassExtractor,
        NonExistingClassConstantExtractor $nonExistingClassConstantExtractor
    ) {
        $this->nonExistingClassExtractor = $nonExistingClassExtractor;
        $this->nonExistingClassConstantExtractor = $nonExistingClassConstantExtractor;
        $this->fileFinder = $fileFinder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Check configs for existing classes');
        $this->addArgument(
            MigrifyOption::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to project'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $source */
        $source = (array) $input->getArgument(MigrifyOption::SOURCES);
        $fileInfos = $this->fileFinder->findInDirectories($source);

        $nonExistingClassesByFile = $this->nonExistingClassExtractor->extractFromFileInfos($fileInfos);
        if ($nonExistingClassesByFile === []) {
            $suffixes = implode(', ', StaticCheckedFileSuffix::SUFFIXES);
            $message = sprintf('All classes in all %s files exist', $suffixes);
            $this->symfonyStyle->success($message);
        }

        $nonExistingClassConstantsByFile = $this->nonExistingClassConstantExtractor->extractFromFileInfos($fileInfos);
        if ($nonExistingClassConstantsByFile === []) {
            $suffixes = implode(', ', StaticCheckedFileSuffix::SUFFIXES);
            $message = sprintf('All class constants in all %s files exist', $suffixes);
            $this->symfonyStyle->success($message);
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
            $message = sprintf('File "%s" contains non-existing classes', $file);
            $this->symfonyStyle->title($message);
            $this->symfonyStyle->listing($nonExistingClasses);
            $this->symfonyStyle->newLine();
        }

        foreach ($nonExistingClassConstantsByFile as $file => $nonExistingClassConstants) {
            $message = sprintf('File "%s" contains non-existing class constants', $file);
            $this->symfonyStyle->title($message);
            $this->symfonyStyle->listing($nonExistingClassConstants);
            $this->symfonyStyle->newLine();
        }

        return ShellCode::ERROR;
    }
}
