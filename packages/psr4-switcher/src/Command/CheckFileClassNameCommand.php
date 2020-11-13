<?php

declare(strict_types=1);

namespace Migrify\Psr4Switcher\Command;

use Migrify\MigrifyKernel\Command\AbstractMigrifyCommand;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Migrify\Psr4Switcher\RobotLoader\PhpClassLoader;
use Nette\Utils\Strings;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class CheckFileClassNameCommand extends AbstractMigrifyCommand
{
    /**
     * @var PhpClassLoader
     */
    private $phpClassLoader;

    public function __construct(PhpClassLoader $phpClassLoader)
    {
        $this->phpClassLoader = $phpClassLoader;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Check if short file name is same as class name');

        $this->addArgument(MigrifyOption::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to source');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument(MigrifyOption::SOURCES);
        $classesToFiles = $this->phpClassLoader->load($sources);

        $missMatchingClassNamesByFiles = [];
        foreach ($classesToFiles as $class => $file) {
            $fileInfo = new SmartFileInfo($file);
            $fileBaseName = $fileInfo->getBasename('.php');
            $shortClassName = Strings::after($class, '\\', -1);
            if ($shortClassName === $fileBaseName) {
                continue;
            }

            $missMatchingClassNamesByFiles[$file] = $class;
        }

        if ($missMatchingClassNamesByFiles === []) {
            $this->symfonyStyle->success('All classes match their short file name');
            return ShellCode::SUCCESS;
        }

        foreach ($missMatchingClassNamesByFiles as $file => $class) {
            $fileInfo = new SmartFileInfo($file);
            $message = sprintf(
                'Check "%s" file to match class name "%s"',
                $fileInfo->getRelativeFilePathFromCwd(),
                $class
            );

            $this->symfonyStyle->warning($message);
        }

        return ShellCode::ERROR;
    }
}
