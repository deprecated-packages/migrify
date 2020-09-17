<?php

declare(strict_types=1);

namespace Migrify\DiffDataMiner\Command;

use Migrify\DiffDataMiner\Extractor\DefaultValueChangesExtractor;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Nette\Utils\Json;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class ExtractDefaultValueChangesCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var DefaultValueChangesExtractor
     */
    private $defaultValueChangesExtractor;

    public function __construct(SymfonyStyle $symfonyStyle, DefaultValueChangesExtractor $defaultValueChangesExtractor)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->defaultValueChangesExtractor = $defaultValueChangesExtractor;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Extra default value changes from .diff');
        $this->addArgument(MigrifyOption::SOURCES, InputArgument::REQUIRED, 'Path to diff file on GitHub');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $source */
        $source = (string) $input->getArgument(MigrifyOption::SOURCES);
        $changedDefaultValues = $this->defaultValueChangesExtractor->extract($source);

        $output = Json::encode($changedDefaultValues, Json::PRETTY);
        $this->symfonyStyle->writeln($output);

        $this->symfonyStyle->success('OK');

        return ShellCode::SUCCESS;
    }
}
