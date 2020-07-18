<?php

declare(strict_types=1);

namespace Migrify\ZephirToPhp\Console\Command;

use Migrify\ZephirToPhp\Finder\ZephirFinder;
use Migrify\ZephirToPhp\ZephirToPhpConverter;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ConvertCommand extends Command
{
    /**
     * @var string
     */
    private const ARGUMENT_SOURCE = 'source';

    /**
     * @var ZephirToPhpConverter
     */
    private $zephirToPhpConverter;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var ZephirFinder
     */
    private $zephirFinder;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        ZephirToPhpConverter $zephirToPhpConverter,
        ZephirFinder $zephirFinder,
        SmartFileSystem $smartFileSystem
    ) {
        $this->zephirToPhpConverter = $zephirToPhpConverter;
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
        $this->zephirFinder = $zephirFinder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(
            self::ARGUMENT_SOURCE,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Directories to convert'
        );
        $this->setDescription('Converts Zephir syntax to PHP');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (array) $input->getArgument(self::ARGUMENT_SOURCE);
        $this->zephirFinder->findInDirectories($source);
        $fileInfos = $this->zephirFinder->findInDirectories($source);

        foreach ($fileInfos as $fileInfo) {
            $convertedContent = $this->zephirToPhpConverter->convertFile($fileInfo);
            $oldFilePath = $fileInfo->getPathname();
            $newFilePath = Strings::replace($fileInfo->getPathname(), '#\.zep#', '.php');

            // save
            $this->smartFileSystem->dumpFile($newFilePath, $convertedContent);

            // remove old path
            $this->smartFileSystem->remove($oldFilePath);

            $message = sprintf('File "%s" was converted to PHP to "%s"', $oldFilePath, $newFilePath);
            $this->symfonyStyle->note($message);
        }

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }
}
