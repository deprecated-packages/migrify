<?php

declare(strict_types=1);

namespace Migrify\SnifferFixerToECS\Command;

use Migrify\MigrifyKernel\Exception\NotImplementedYetException;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Migrify\SnifferFixerToECS\FixerToECSConverter;
use Migrify\SnifferFixerToECS\SnifferToECSConverter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ConvertCommand extends Command
{
    /**
     * @var SnifferToECSConverter
     */
    private $snifferToECSConverter;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var FixerToECSConverter
     */
    private $fixerToECSConverter;

    public function __construct(
        SnifferToECSConverter $snifferToECSConverter,
        FixerToECSConverter $fixerToECSConverter,
        SymfonyStyle $symfonyStyle,
        SmartFileSystem $smartFileSystem
    ) {
        $this->snifferToECSConverter = $snifferToECSConverter;
        $this->fixerToECSConverter = $fixerToECSConverter;
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(
            MigrifyOption::SOURCES,
            InputArgument::REQUIRED,
            'File to convert, usually "phpcs.xml" or ".php_cs.dist"'
        );
        $this->setDescription('Converts PHP_CodeSniffer or PHP-CS-Fixer config to ECS one - ecs.php');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(MigrifyOption::SOURCES);
        if (! $this->smartFileSystem->exists($source)) {
            throw new FileNotFoundException($source);
        }

        $sourceFileInfo = new SmartFileInfo($source);
        if ($sourceFileInfo->getSuffix() === 'xml') {
            $convertedECSFileContent = $this->snifferToECSConverter->convertFile($sourceFileInfo);
        } elseif (in_array($sourceFileInfo->getSuffix(), ['php_cs', 'dist'], true)) {
            $convertedECSFileContent = $this->fixerToECSConverter->convertFile($sourceFileInfo);
        } else {
            $message = sprintf('File "%s" has not matched any converted.', $source);
            throw new NotImplementedYetException($message);
        }

        $outputFileName = $sourceFileInfo->getPath() . DIRECTORY_SEPARATOR . 'converted-ecs.php';
        $this->smartFileSystem->dumpFile($outputFileName, $convertedECSFileContent);

        $message = sprintf('"%s" was converted into "%s"', $source, $outputFileName);
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
