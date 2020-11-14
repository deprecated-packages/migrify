<?php

declare(strict_types=1);

namespace Migrify\PHPMDDecomposer\Command;

use Migrify\MigrifyKernel\Command\AbstractMigrifyCommand;
use Migrify\MigrifyKernel\Exception\ShouldNotHappenException;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Migrify\PHPMDDecomposer\PHPMDDecomposer;
use Migrify\PHPMDDecomposer\Printer\PHPStanPrinter;
use Migrify\PHPMDDecomposer\ValueObject\DecomposedFileConfigs;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;

final class DecomposeCommand extends AbstractMigrifyCommand
{
    /**
     * @var PHPMDDecomposer
     */
    private $phpmdDecomposer;

    /**
     * @var PHPStanPrinter
     */
    private $phpStanPrinter;

    public function __construct(
        FileSystemGuard $fileSystemGuard,
        PHPMDDecomposer $phpmdDecomposer,
        PHPStanPrinter $phpStanPrinter
    ) {
        $this->fileSystemGuard = $fileSystemGuard;
        $this->phpmdDecomposer = $phpmdDecomposer;

        $this->phpStanPrinter = $phpStanPrinter;

        parent::__construct();
    }

    protected function configure(): void
    {
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
