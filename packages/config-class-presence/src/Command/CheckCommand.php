<?php

declare(strict_types=1);

namespace Migrify\ConfigClassPresence\Command;

use Migrify\ConfigClassPresence\Finder\ConfigFinder;
use Migrify\ConfigClassPresence\Regex\NonExistingClassExtractor;
use Migrify\ConfigClassPresence\ValueObject\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
     * @var ConfigFinder
     */
    private $configFinder;

    /**
     * @var NonExistingClassExtractor
     */
    private $nonExistingClassExtractor;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        ConfigFinder $configFinder,
        NonExistingClassExtractor $classNameExtractor
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->configFinder = $configFinder;
        $this->nonExistingClassExtractor = $classNameExtractor;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Check configs for existing classes');
        $this->addArgument(Option::SOURCE, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to project');

        $this->addOption(Option::AUTOLOAD_FILE, 'a', InputOption::VALUE_REQUIRED, 'Path to autoload file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $autoloadFile = $input->getOption(Option::AUTOLOAD_FILE);
        if (is_string($autoloadFile)) {
            include_once $autoloadFile;
        }

        /** @var string[] $source */
        $source = (array) $input->getArgument(Option::SOURCE);

        $configFileInfos = $this->configFinder->findIn($source);
        $nonExistingClassesByFile = $this->nonExistingClassExtractor->extractFromFileInfos($configFileInfos);

        if ($nonExistingClassesByFile === []) {
            $this->symfonyStyle->success('All classes in all configs exist');

            return ShellCode::SUCCESS;
        }

        foreach ($nonExistingClassesByFile as $file => $nonExistingClasses) {
            $this->symfonyStyle->title(sprintf('File "%s" contains non-existing classes', $file));
            $this->symfonyStyle->listing($nonExistingClasses);
            $this->symfonyStyle->newLine();
        }

        return ShellCode::ERROR;
    }
}
