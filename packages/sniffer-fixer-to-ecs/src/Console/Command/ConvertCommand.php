<?php

declare(strict_types=1);

namespace Migrify\SnifferFixerToECS\Console\Command;

use Migrify\NeonToYaml\Exception\NotImplementedYetException;
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
     * @var string
     */
    private const ARGUMENT_SOURCE = 'source';

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

    public function __construct(
        SnifferToECSConverter $snifferToECSConverter,
        SymfonyStyle $symfonyStyle,
        SmartFileSystem $smartFileSystem
    ) {
        $this->snifferToECSConverter = $snifferToECSConverter;
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(
            self::ARGUMENT_SOURCE,
            InputArgument::REQUIRED,
            'File to convert, usually phpcs.xml or php-cs-fixer.php'
        );
        $this->setDescription('Converts PHP_CodeSniffer or PHP-CS-Fixer config to ECS one - ecs.php');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(self::ARGUMENT_SOURCE);
        if (! $this->smartFileSystem->exists($source)) {
            throw new FileNotFoundException($source);
        }

        $sourceFileInfo = new SmartFileInfo($source);
        if ($sourceFileInfo->getSuffix() === 'xml') {
            $convertedECSFileContent = $this->snifferToECSConverter->convertFile($sourceFileInfo);

            $outputFileName = $sourceFileInfo->getPath() . DIRECTORY_SEPARATOR . 'converted-ecs.php';
            $this->smartFileSystem->dumpFile($outputFileName, $convertedECSFileContent);
        } else {
            throw new NotImplementedYetException();
        }

        $message = sprintf('"%s" was converted into "%s"', $source, $outputFileName);
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
