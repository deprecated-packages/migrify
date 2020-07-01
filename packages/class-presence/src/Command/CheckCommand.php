<?php

declare(strict_types=1);

namespace Migrify\ClassPresence\Command;

use Migrify\ClassPresence\Finder\FileFinder;
use Migrify\ClassPresence\Regex\NonExistingClassConstantExtractor;
use Migrify\ClassPresence\Regex\NonExistingClassExtractor;
use Migrify\ClassPresence\ValueObject\Option;
use Migrify\ClassPresence\ValueObject\StaticCheckedFileSuffix;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class CheckCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

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
        SymfonyStyle $symfonyStyle,
        FileFinder $fileFinder,
        NonExistingClassExtractor $classNameExtractor,
        NonExistingClassConstantExtractor $nonExistingClassConstantExtractor
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->nonExistingClassExtractor = $classNameExtractor;
        $this->nonExistingClassConstantExtractor = $nonExistingClassConstantExtractor;

        parent::__construct();

        $this->fileFinder = $fileFinder;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Check configs for existing classes');
        $this->addArgument(Option::SOURCE, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to project');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $source */
        $source = (array) $input->getArgument(Option::SOURCE);

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
            $this->symfonyStyle->title(sprintf('File "%s" contains non-existing classes', $file));
            $this->symfonyStyle->listing($nonExistingClasses);
            $this->symfonyStyle->newLine();
        }

        foreach ($nonExistingClassConstantsByFile as $file => $nonExistingClassConstants) {
            $this->symfonyStyle->title(sprintf('File "%s" contains non-existing class constants', $file));
            $this->symfonyStyle->listing($nonExistingClassConstants);
            $this->symfonyStyle->newLine();
        }

        return ShellCode::ERROR;
    }
}
