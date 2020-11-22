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
        /** @var string[] $sources */
        $sources = (array) $input->getArgument(MigrifyOption::SOURCES);
        $fileInfos = $this->smartFinder->find($sources, $this->suffixes->provideRegex());

        $message = sprintf(
            'Checking %d files with "%s" suffixes',
            count($fileInfos),
            implode('", "', $this->suffixes->provide())
        );
        $this->symfonyStyle->note($message);

        $nonExistingClassesByFile = $this->nonExistingClassExtractor->extractFromFileInfos($fileInfos);
        $nonExistingClassConstantsByFile = $this->nonExistingClassConstantExtractor->extractFromFileInfos($fileInfos);

        if ($nonExistingClassConstantsByFile === [] && $nonExistingClassesByFile === []) {
            $this->symfonyStyle->success('All classes and class constants exists');
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
        $i = 0;

        foreach ($nonExistingClassesByFile as $file => $nonExistingClasses) {
            $fileMssage = sprintf('<options=bold>%d) %s</>', ++$i, $file);
            $this->symfonyStyle->writeln($fileMssage);
            $this->symfonyStyle->newLine();

            foreach ($nonExistingClasses as $nonExistingClass) {
                $errorMessage = sprintf('Class "%s" not found', $nonExistingClass);
                $this->symfonyStyle->error($errorMessage);
            }
        }

        foreach ($nonExistingClassConstantsByFile as $file => $nonExistingClassConstants) {
            $fileMssage = sprintf('<options=bold>%d) %s</>', ++$i, $file);
            $this->symfonyStyle->writeln($fileMssage);

            foreach ($nonExistingClassConstants as $nonExistingClassConstant) {
                $errorMessage = sprintf('Class constant "%s" does not exist', $nonExistingClassConstant);
                $this->symfonyStyle->error($errorMessage);
            }
        }

        return ShellCode::ERROR;
    }
}
