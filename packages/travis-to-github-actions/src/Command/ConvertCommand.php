<?php

declare(strict_types=1);

namespace Migrify\TravisToGithubActions\Command;

use Migrify\MigrifyKernel\Command\AbstractMigrifyCommand;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Migrify\TravisToGithubActions\TravisToGithubActionsConverter;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConvertCommand extends AbstractMigrifyCommand
{
    /**
     * @var TravisToGithubActionsConverter
     */
    private $travisToGithubActionsConverter;

    public function __construct(
        TravisToGithubActionsConverter $travisToGithubActionsConverter,
        FileSystemGuard $fileSystemGuard
    ) {
        parent::__construct();

        $this->travisToGithubActionsConverter = $travisToGithubActionsConverter;

        $this->fileSystemGuard = $fileSystemGuard;
    }

    protected function configure(): void
    {
        $this->addArgument(MigrifyOption::SOURCES, InputArgument::REQUIRED, 'Directory or file to convert');
        $this->setDescription('Converts Neon syntax to Yaml');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(MigrifyOption::SOURCES);
        $this->fileSystemGuard->ensureFileExists($source, __METHOD__);

        $inputFileInfo = new SmartFileInfo($source);
        $convertedContent = $this->travisToGithubActionsConverter->convert($inputFileInfo);

        $pathname = $inputFileInfo->getPathname();

        $codeChecksWorkflowFilePath = $pathname . '/.github/workflows/code_checks.yaml';
        $this->smartFileSystem->dumpFile($convertedContent, $codeChecksWorkflowFilePath);

        $message = sprintf('File %s was created', $codeChecksWorkflowFilePath);
        $this->symfonyStyle->success($message);

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }
}
