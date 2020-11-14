<?php

declare(strict_types=1);

namespace Migrify\LatteToTwig\Command;

use Migrify\LatteToTwig\Finder\LatteAndTwigFinder;
use Migrify\LatteToTwig\LatteToTwigConverter;
use Migrify\MigrifyKernel\Command\AbstractMigrifyCommand;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Nette\Utils\Strings;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\ShellCode;

final class ConvertCommand extends AbstractMigrifyCommand
{
    /**
     * @var LatteToTwigConverter
     */
    private $latteToTwigConverter;

    /**
     * @var LatteAndTwigFinder
     */
    private $latteAndTwigFinder;

    public function __construct(LatteToTwigConverter $latteToTwigConverter, LatteAndTwigFinder $latteAndTwigFinder)
    {
        $this->latteToTwigConverter = $latteToTwigConverter;
        $this->latteAndTwigFinder = $latteAndTwigFinder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(MigrifyOption::SOURCES, InputArgument::REQUIRED, 'Directory or file to convert');
        $this->setDescription('Converts Latte syntax to Twig');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(MigrifyOption::SOURCES);
        $fileInfos = $this->latteAndTwigFinder->findTwigAndLatteFilesInSource($source);

        foreach ($fileInfos as $fileInfo) {
            $convertedContent = $this->latteToTwigConverter->convertFile($fileInfo);
            $oldFilePath = $fileInfo->getPathname();
            $newFilePath = Strings::replace($fileInfo->getPathname(), '#\.latte$#', '.twig');

            // save
            $this->smartFileSystem->dumpFile($newFilePath, $convertedContent);

            // remove old path
            if ($oldFilePath !== $newFilePath) {
                $this->smartFileSystem->remove($oldFilePath);
            }
            $message = sprintf('File "%s" was converted to Twig to "%s"', $oldFilePath, $newFilePath);

            $this->symfonyStyle->note($message);
        }

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }
}
