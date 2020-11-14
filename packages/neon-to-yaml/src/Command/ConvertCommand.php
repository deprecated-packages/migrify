<?php

declare(strict_types=1);

namespace Migrify\NeonToYaml\Command;

use Migrify\MigrifyKernel\Command\AbstractMigrifyCommand;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Migrify\NeonToYaml\ArrayParameterCollector;
use Migrify\NeonToYaml\Finder\NeonAndYamlFinder;
use Migrify\NeonToYaml\NeonToYamlConverter;
use Nette\Utils\Strings;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\ShellCode;

final class ConvertCommand extends AbstractMigrifyCommand
{
    /**
     * @var NeonToYamlConverter
     */
    private $neonToYamlConverter;

    /**
     * @var NeonAndYamlFinder
     */
    private $neonAndYamlFinder;

    /**
     * @var ArrayParameterCollector
     */
    private $arrayParameterCollector;

    public function __construct(
        NeonToYamlConverter $neonToYamlConverter,
        NeonAndYamlFinder $neonAndYamlFinder,
        ArrayParameterCollector $arrayParameterCollector
    ) {
        parent::__construct();

        $this->neonToYamlConverter = $neonToYamlConverter;
        $this->neonAndYamlFinder = $neonAndYamlFinder;
        $this->arrayParameterCollector = $arrayParameterCollector;
    }

    protected function configure(): void
    {
        $this->addArgument(MigrifyOption::SOURCES, InputArgument::REQUIRED, 'Directory or file to convert');
        $this->setDescription('Converts Neon syntax to Yaml');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(MigrifyOption::SOURCES);
        $fileInfos = $this->neonAndYamlFinder->findYamlAndNeonFilesInSource($source);

        $this->arrayParameterCollector->collectFromFiles($fileInfos);

        foreach ($fileInfos as $fileInfo) {
            $convertedContent = $this->neonToYamlConverter->convertFileInfo($fileInfo);
            $oldFilePath = $fileInfo->getPathname();
            $newFilePath = Strings::replace($oldFilePath, '#\.neon$#', '.yaml');

            // save
            $this->smartFileSystem->dumpFile($newFilePath, $convertedContent);

            // remove old path
            if ($oldFilePath !== $newFilePath) {
                $this->smartFileSystem->remove($oldFilePath);
            }
            $message = sprintf('File "%s" was converted to YAML to "%s" path', $oldFilePath, $newFilePath);

            $this->symfonyStyle->note($message);
        }

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }
}
