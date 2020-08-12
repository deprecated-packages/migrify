<?php

declare(strict_types=1);

namespace Migrify\EasyCI\Command;

use Migrify\EasyCI\Finder\SrcTestsDirectoriesFinder;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class GenerateSonarProjectCommand extends Command
{
    /**
     * @var string[]
     */
    private const POSSIBLE_DIRECTORIES = ['src', 'tests', 'packages'];

    /**
     * @var string
     */
    private const SONAR_PROJECT_PROPERTIES = 'sonar-project.properties';

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var SrcTestsDirectoriesFinder
     */
    private $srcTestsDirectoriesFinder;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        SmartFileSystem $smartFileSystem,
        SrcTestsDirectoriesFinder $srcTestsDirectoriesFinder
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
        $this->srcTestsDirectoriesFinder = $srcTestsDirectoriesFinder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));

        $description = sprintf('Generate "%s" path', self::SONAR_PROJECT_PROPERTIES);
        $this->setDescription($description);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $srcAndTestsDirectories = $this->srcTestsDirectoriesFinder->findSrcAndTestsDirectories(
            self::POSSIBLE_DIRECTORIES
        );

        if ($srcAndTestsDirectories === null) {
            $warning = sprintf(
                'No "src"/"tests" directories found in "%s" paths',
                implode('", ', self::POSSIBLE_DIRECTORIES)
            );
            $this->symfonyStyle->warning($warning);

            return ShellCode::SUCCESS;
        }

        $filePath = getcwd() . '/' . self::SONAR_PROJECT_PROPERTIES;
        $fileContent = $this->smartFileSystem->readFile($filePath);

        $originalFileContent = $fileContent;

        if ($srcAndTestsDirectories->getSrcDirectories() !== []) {
            $sonarPathLine = $this->buildSonarPathLine($srcAndTestsDirectories->getSrcDirectories());
            $fileContent = Strings::replace($fileContent, '#(sonar\.sources\=)(.*?)$#', '$1' . $sonarPathLine);
        }

        if ($srcAndTestsDirectories->getTestsDirectories() !== []) {
            $sonarPathLine = $this->buildSonarPathLine($srcAndTestsDirectories->getTestsDirectories());
            $fileContent = Strings::replace($fileContent, '#(sonar\.tests\=)(.*?)$#', '$1' . $sonarPathLine);
        }

        if ($originalFileContent === $fileContent) {
            $this->symfonyStyle->success('Nothing to change');
            return ShellCode::SUCCESS;
        }

        $this->smartFileSystem->dumpFile($filePath, $fileContent);

        $message = sprintf('File "%s" was updated with new paths', $filePath);
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }

    /**
     * @param SmartFileInfo[] $directoryFileInfos
     */
    private function buildSonarPathLine(array $directoryFileInfos): string
    {
        $relativePaths = [];
        foreach ($directoryFileInfos as $directoryFileInfo) {
            $relativePaths[] = $directoryFileInfo->getRelativeFilePathFromCwd();
        }

        sort($relativePaths);

        return implode(',', $relativePaths);
    }
}
