<?php

declare(strict_types=1);

namespace Migrify\ConfigFormatConverter\Command;

use Migrify\ConfigFormatConverter\Converter\ConfigFormatConverter;
use Migrify\ConfigFormatConverter\Finder\ConfigFileFinder;
use Migrify\ConfigFormatConverter\ValueObject\Option;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConvertCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ConfigFileFinder
     */
    private $configFileFinder;

    /**
     * @var ConfigFormatConverter
     */
    private $configFormatConverter;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        ConfigFileFinder $configFileFinder,
        ConfigFormatConverter $configFormatConverter
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->configFileFinder = $configFileFinder;
        $this->configFormatConverter = $configFormatConverter;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Converts all XML files to YAML format');
        $this->addArgument(Option::SOURCE, InputArgument::REQUIRED, 'Path to directory with configs');
        $this->addOption(Option::FORMAT, null, InputOption::VALUE_REQUIRED, 'Config format to output', 'yaml');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $source */
        $source = (string) $input->getArgument(Option::SOURCE);

        /** @var string $outputFormat */
        $outputFormat = (string) $input->getArgument(Option::FORMAT);

        $fileInfos = $this->configFileFinder->findInDirectory($source);
        foreach ($fileInfos as $fileInfo) {
            $convertedContent = $this->configFormatConverter->convert($fileInfo, $outputFormat);

            // dump the file
            $fileName = $fileInfo->getFilenameWithoutExtension() . '.' . $outputFormat;
            FileSystem::write($fileName, $convertedContent);

            $newFileInfo = new SmartFileInfo($fileName);
            $message = sprintf('File "%s" was dumped', $newFileInfo->getRelativeFilePathFromCwd());
            $this->symfonyStyle->writeln($message);
        }

        $this->symfonyStyle->success('OK');

        return ShellCode::SUCCESS;
    }
}
