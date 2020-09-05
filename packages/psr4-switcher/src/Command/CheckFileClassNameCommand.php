<?php

declare(strict_types=1);

namespace Migrify\Psr4Switcher\Command;

use Migrify\Psr4Switcher\RobotLoader\PhpClassLoader;
use Migrify\Psr4Switcher\ValueObject\Option;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class CheckFileClassNameCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var PhpClassLoader
     */
    private $phpClassLoader;

    public function __construct(SymfonyStyle $symfonyStyle, PhpClassLoader $phpClassLoader)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->phpClassLoader = $phpClassLoader;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Check if short file name is same as class name');

        $this->addArgument(Option::SOURCE, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to source');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $this->getSource($input);
        $classesToFiles = $this->phpClassLoader->load($source);

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

    /**
     * @return string[]
     */
    private function getSource(InputInterface $input): array
    {
        return (array) $input->getArgument(Option::SOURCE);
    }
}
