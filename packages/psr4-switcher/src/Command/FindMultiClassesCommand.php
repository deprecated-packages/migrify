<?php

declare(strict_types=1);

namespace Migrify\Psr4Switcher\Command;

use Migrify\Psr4Switcher\Finder\MultipleClassInOneFileFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class FindMultiClassesCommand extends Command
{
    /**
     * @var string
     */
    private const SOURCE = 'source';

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var MultipleClassInOneFileFinder
     */
    private $multipleClassInOneFileFinder;

    public function __construct(SymfonyStyle $symfonyStyle, MultipleClassInOneFileFinder $multipleClassInOneFileFinder)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->multipleClassInOneFileFinder = $multipleClassInOneFileFinder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Find multiple classes in one file');
        $this->addArgument(
            self::SOURCE,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to source to analyse'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $source */
        $source = $input->getArgument(self::SOURCE);

        $multipleClassesByFile = $this->multipleClassInOneFileFinder->findInDirectories($source);
        if ($multipleClassesByFile === []) {
            $this->symfonyStyle->success('No files with 2+ classes found');

            return ShellCode::SUCCESS;
        }

        foreach ($multipleClassesByFile as $file => $classes) {
            $message = sprintf('File "%s" has %d classes', $file, count($classes));
            $this->symfonyStyle->section($message);
            $this->symfonyStyle->listing($classes);
        }

        return ShellCode::ERROR;
    }
}
