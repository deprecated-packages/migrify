<?php

declare(strict_types=1);

namespace Migrify\PHPMDDecomposer\Command;

use Migrify\MigrifyKernel\Exception\ShouldNotHappenException;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Migrify\PHPMDDecomposer\PHPMDDecomposer;
use Migrify\PHPMDDecomposer\Printer\PHPStanPrinter;
use Migrify\PHPMDDecomposer\ValueObject\DecomposedFileConfigs;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class DecomposeCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var PHPMDDecomposer
     */
    private $phpmdDecomposer;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var PHPStanPrinter
     */
    private $phpStanPrinter;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        FileSystemGuard $fileSystemGuard,
        PHPMDDecomposer $phpmdDecomposer,
        SmartFileSystem $smartFileSystem,
        PHPStanPrinter $phpStanPrinter
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->fileSystemGuard = $fileSystemGuard;
        $this->phpmdDecomposer = $phpmdDecomposer;
        $this->smartFileSystem = $smartFileSystem;
        $this->phpStanPrinter = $phpStanPrinter;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(MigrifyOption::SOURCES, InputArgument::REQUIRED, 'File path to phpmd.xml to convert');
        $this->setDescription('Converts phpmd.xml to phpstan.neon, ecs.php and rector.php');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(MigrifyOption::SOURCES);
        $this->fileSystemGuard->ensureFileExists($source, __METHOD__);

        $phpmdXmlFileInfo = new SmartFileInfo($source);
        if ($phpmdXmlFileInfo->getSuffix() !== 'xml') {
            throw new ShouldNotHappenException();
        }

        $decomposedFileConfigs = $this->phpmdDecomposer->decompose($phpmdXmlFileInfo);

        // @todo for all files
        $this->printPHPStanConfig($decomposedFileConfigs, $phpmdXmlFileInfo);

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }

    private function printPHPStanConfig(
        DecomposedFileConfigs $decomposedFileConfigs,
        SmartFileInfo $phpmdXmlFileInfo
    ): void {
        $phpstanConfig = $decomposedFileConfigs->getPHPStanConfig();
        if (! $phpstanConfig->isEmpty()) {
            $path = $phpmdXmlFileInfo->getPath();
            $phpstanFilePath = $path . '/phpmd-decomposed-phpstan.neon';

            $phpStanFileContent = $this->phpStanPrinter->printPHPStanConfig($phpstanConfig);
            $this->smartFileSystem->dumpFile($phpstanFilePath, $phpStanFileContent);
        }
    }
}
