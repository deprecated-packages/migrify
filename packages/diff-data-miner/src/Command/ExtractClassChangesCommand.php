<?php

declare(strict_types=1);

namespace Migrify\DiffDataMiner\Command;

use Migrify\DiffDataMiner\Extractor\ClassChangesExtractor;
use Migrify\DiffDataMiner\ValueObject\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class ExtractClassChangesCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ClassChangesExtractor
     */
    private $classChangesExtractor;

    public function __construct(SymfonyStyle $symfonyStyle, ClassChangesExtractor $classChangesExtractor)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->classChangesExtractor = $classChangesExtractor;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Scan for changes class names');
        $this->addArgument(Option::SOURCE, InputArgument::REQUIRED, 'Path to diff file on GitHub');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $source */
        $source = (string) $input->getArgument(Option::SOURCE);
        $changedClasses = $this->classChangesExtractor->extract($source);

        $yaml = Yaml::dump($changedClasses);
        $this->symfonyStyle->writeln($yaml);

        $this->symfonyStyle->success('OK');

        return ShellCode::SUCCESS;
    }
}
