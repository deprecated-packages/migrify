<?php

declare(strict_types=1);

namespace Migrify\DiffDataMiner\Command;

use Migrify\DiffDataMiner\Extractor\ClassChangesExtractor;
use Migrify\MigrifyKernel\Command\AbstractMigrifyCommand;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Symplify\PackageBuilder\Console\ShellCode;

final class ExtractClassChangesCommand extends AbstractMigrifyCommand
{
    /**
     * @var ClassChangesExtractor
     */
    private $classChangesExtractor;

    public function __construct(ClassChangesExtractor $classChangesExtractor)
    {
        $this->classChangesExtractor = $classChangesExtractor;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Scan for changes class names');
        $this->addArgument(MigrifyOption::SOURCES, InputArgument::REQUIRED, 'Path to diff file on GitHub');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $source */
        $source = (string) $input->getArgument(MigrifyOption::SOURCES);
        $changedClasses = $this->classChangesExtractor->extract($source);

        $yaml = Yaml::dump($changedClasses);
        $this->symfonyStyle->writeln($yaml);

        $this->symfonyStyle->success('OK');

        return ShellCode::SUCCESS;
    }
}
