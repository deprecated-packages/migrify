<?php

declare(strict_types=1);

namespace Migrify\ConfigTransformer\Command;

use Migrify\ConfigTransformer\Configuration\Configuration;
use Migrify\ConfigTransformer\Converter\ConfigFormatConverter;
use Migrify\ConfigTransformer\ValueObject\Format;
use Migrify\ConfigTransformer\ValueObject\Option;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class SwitchFormatCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ConfigFormatConverter
     */
    private $configFormatConverter;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var SmartFinder
     */
    private $smartFinder;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        ConfigFormatConverter $configFormatConverter,
        Configuration $configuration,
        SmartFinder $smartFinder,
        SmartFileSystem $smartFileSystem
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->configFormatConverter = $configFormatConverter;
        $this->configuration = $configuration;
        $this->smartFileSystem = $smartFileSystem;
        $this->smartFinder = $smartFinder;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Converts XML/YAML configs to YAML/PHP format');

        $this->addArgument(
            MigrifyOption::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to directory with configs'
        );

        $this->addOption(Option::INPUT_FORMAT, 'i', InputOption::VALUE_REQUIRED, 'Config format to input');
        $this->addOption(
            Option::OUTPUT_FORMAT,
            'o',
            InputOption::VALUE_REQUIRED,
            'Config format to output',
            Format::PHP
        );

        $this->addOption(
            Option::BC_LAYER,
            null,
            InputOption::VALUE_NONE,
            'Keep original config with include of new one, to prevent breaking of old config paths'
        );

        $this->addOption(
            Option::TARGET_SYMFONY_VERSION,
            's',
            InputOption::VALUE_REQUIRED,
            'Symfony version to migrate config to',
            3.2
        );

        $this->addOption(Option::DRY_RUN, null, InputOption::VALUE_NONE, 'Dry run - no removal or config change');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configuration->populateFromInput($input);

        $suffixes = $this->configuration->getInputSuffixes();
        $suffixesRegex = '#\.' . implode('|', $suffixes) . '$#';
        $fileInfos = $this->smartFinder->find($this->configuration->getSource(), $suffixesRegex);

        $this->dumpNewFileInfos($fileInfos);
        $this->processOldFileInfos($fileInfos);

        $successMessage = sprintf(
            'Processed %d file(s) from "%s" to "%s" format',
            count($fileInfos),
            $this->configuration->getInputFormat(),
            $this->configuration->getOutputFormat()
        );
        $this->symfonyStyle->success($successMessage);

        return ShellCode::SUCCESS;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
    private function processOldFileInfos(array $fileInfos): void
    {
        if ($this->configuration->isDryRun()) {
            return;
        }

        if (count($fileInfos) === 0) {
            return;
        }

        if ($this->configuration->shouldKeepBcLayer()) {
            foreach ($fileInfos as $fileInfo) {
                $yamlContent = $this->crateYamlWithPhpFileImport($fileInfo);
                $this->smartFileSystem->dumpFile($fileInfo->getRealPath(), $yamlContent);
            }

            $updatedFilesMessage = sprintf('updated %d with BC layer to new configs', count($fileInfos));
            $this->symfonyStyle->warning($updatedFilesMessage);
        } else {
            $this->smartFileSystem->remove($fileInfos);
            $deletedFilesMessage = sprintf('Deleted %d original file(s)', count($fileInfos));
            $this->symfonyStyle->warning($deletedFilesMessage);
        }
    }

    private function dumpFile(SmartFileInfo $fileInfo, string $convertedContent): void
    {
        $fileRealPathWithoutSuffix = Strings::replace($fileInfo->getRealPath(), '#\.[^.]+$#');
        $newFilePath = $fileRealPathWithoutSuffix . '.' . $this->configuration->getOutputFormat();

        $relativeFilePath = $this->getRelativePathOfNonExistingFile($newFilePath);

        if ($this->configuration->isDryRun()) {
            $message = sprintf('File "%s" would be dumped (is --dry-run)', $relativeFilePath);
            $this->symfonyStyle->note($message);
            return;
        }

        $this->smartFileSystem->dumpFile($newFilePath, $convertedContent);

        $message = sprintf('File "%s" was dumped', $relativeFilePath);
        $this->symfonyStyle->note($message);
    }

    private function getRelativePathOfNonExistingFile(string $newFilePath): string
    {
        $relativeFilePath = $this->smartFileSystem->makePathRelative($newFilePath, getcwd());
        return rtrim($relativeFilePath, '/');
    }

    private function crateYamlWithPhpFileImport(SmartFileInfo $fileInfo): string
    {
        $yamlImportData = [
            'imports' => [
                [
                    'resource' => $fileInfo->getBasenameWithoutSuffix() . '.php',
                ],
            ],
        ];

        return Yaml::dump($yamlImportData) . PHP_EOL;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
    private function dumpNewFileInfos(array $fileInfos): void
    {
        foreach ($fileInfos as $fileInfo) {
            $message = sprintf('Processing "%s" file', $fileInfo->getRelativeFilePathFromCwd());
            $this->symfonyStyle->note($message);

            $convertedContent = $this->configFormatConverter->convert(
                $fileInfo,
                $this->configuration->getInputFormat(),
                $this->configuration->getOutputFormat()
            );

            $this->dumpFile($fileInfo, $convertedContent);
        }
    }
}
