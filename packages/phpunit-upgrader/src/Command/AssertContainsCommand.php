<?php

declare(strict_types=1);

namespace Migrify\PHPUnitUpgrader\Command;

use Migrify\MigrifyKernel\Command\AbstractMigrifyCommand;
use Migrify\PHPUnitUpgrader\FileInfoDecorator\AssertContainsInfoDecorator;
use Migrify\PHPUnitUpgrader\ValueObject\FilePathWithContent;
use Migrify\PHPUnitUpgrader\ValueObject\Option;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ConsoleColorDiff\Console\Output\ConsoleDiffer;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AssertContainsCommand extends AbstractMigrifyCommand
{
    /**
     * @var ConsoleDiffer
     */
    private $consoleDiffer;

    /**
     * @var AssertContainsInfoDecorator
     */
    private $assertContainsInfoDecorator;

    public function __construct(
        AssertContainsInfoDecorator $assertContainsInfoDecorator,
        ConsoleDiffer $consoleDiffer
    ) {
        $this->assertContainsInfoDecorator = $assertContainsInfoDecorator;
        $this->consoleDiffer = $consoleDiffer;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Change assertContains() to assertStringContainsString() where reported by PHPUnit');
        $this->addArgument(
            Option::SOURCE,
            InputArgument::REQUIRED,
            'Path to error output from PHPUnit with assertContains() report'
        );

        $this->addOption(
            Option::ERROR_REPORT_FILE,
            null,
            InputOption::VALUE_REQUIRED,
            'Path to PHPUnit report file to extract assertString() failure from'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(Option::SOURCE);
        $this->fileSystemGuard->ensureFileExists($source, __METHOD__);

        $testFileInfos = $this->smartFinder->find([$source], '#Test\.php#');

        $errorReportFile = (string) $input->getOption(Option::ERROR_REPORT_FILE);
        $this->fileSystemGuard->ensureFileExists($errorReportFile, __METHOD__);

        $errorReportFileInfo = new SmartFileInfo($errorReportFile);

        foreach ($testFileInfos as $testFileInfo) {
            $filePathWithContent = new FilePathWithContent(
                $testFileInfo->getRelativeFilePathFromCwd(),
                $testFileInfo->getContents()
            );

            $changedContent = $this->assertContainsInfoDecorator->decorate($filePathWithContent, $errorReportFileInfo);
            if ($changedContent === $testFileInfo->getContents()) {
                continue;
            }

            $this->processChangedFileInfo($testFileInfo, $changedContent);
        }

        $this->symfonyStyle->success('assertContains() was converted to assertStringContainsString() where needed');

        return ShellCode::SUCCESS;
    }

    private function processChangedFileInfo(SmartFileInfo $testFileInfo, string $changedContent): void
    {
        $this->symfonyStyle->newLine();
        $this->consoleDiffer->diff($testFileInfo->getContents(), $changedContent);

        // update file content
        $this->smartFileSystem->dumpFile($testFileInfo->getPathname(), $changedContent);
        $message = sprintf('File "%s" was updated', $testFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->success($message);
    }
}
