<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker\Command;

use Migrify\TemplateChecker\Analyzer\MissingClassConstantLatteAnalyzer;
use Migrify\TemplateChecker\Analyzer\MissingClassesLatteAnalyzer;
use Migrify\TemplateChecker\Analyzer\MissingClassStaticCallLatteAnalyzer;
use Migrify\TemplateChecker\Finder\GenericFilesFinder;
use Migrify\TemplateChecker\ValueObject\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class CheckLatteTemplateCommand extends Command
{
    /**
     * @var GenericFilesFinder
     */
    private $genericFilesFinder;

    /**
     * @var MissingClassConstantLatteAnalyzer
     */
    private $missingClassConstantLatteAnalyzer;

    /**
     * @var MissingClassesLatteAnalyzer
     */
    private $missingClassesLatteAnalyzer;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var MissingClassStaticCallLatteAnalyzer
     */
    private $missingClassStaticCallLatteAnalyzer;

    public function __construct(
        GenericFilesFinder $genericFilesFinder,
        MissingClassConstantLatteAnalyzer $missingClassConstantLatteAnalyzer,
        MissingClassesLatteAnalyzer $missingClassesLatteAnalyzer,
        MissingClassStaticCallLatteAnalyzer $missingClassStaticCallLatteAnalyzer,
        SymfonyStyle $symfonyStyle
    ) {
        $this->genericFilesFinder = $genericFilesFinder;
        $this->missingClassConstantLatteAnalyzer = $missingClassConstantLatteAnalyzer;
        $this->missingClassesLatteAnalyzer = $missingClassesLatteAnalyzer;
        $this->symfonyStyle = $symfonyStyle;
        $this->missingClassStaticCallLatteAnalyzer = $missingClassStaticCallLatteAnalyzer;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more paths with templates'
        );
        $this->setDescription('Analyze missing classes, constant and static calls in Latte templates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sources = (array) $input->getArgument(Option::SOURCES);
        $latteFileInfos = $this->genericFilesFinder->find($sources, '*.latte');

        $message = sprintf('Analysing %d *.latte files', count($latteFileInfos));
        $this->symfonyStyle->note($message);

        $errors = $this->analyzeFileInfosForErrors($latteFileInfos);

        if ($errors === []) {
            $this->symfonyStyle->success('No errors found');
            return ShellCode::SUCCESS;
        }

        foreach ($errors as $error) {
            $this->symfonyStyle->note($error);
        }

        $errorMassage = sprintf('%d errors found', count($errors));
        $this->symfonyStyle->error($errorMassage);

        return ShellCode::ERROR;
    }

    /**
     * @param SmartFileInfo[] $latteFileInfos
     * @return string[]
     */
    private function analyzeFileInfosForErrors(array $latteFileInfos): array
    {
        $errors = [];
        $errors += $this->missingClassesLatteAnalyzer->analyze($latteFileInfos);
        $errors += $this->missingClassConstantLatteAnalyzer->analyze($latteFileInfos);
        $errors += $this->missingClassStaticCallLatteAnalyzer->analyze($latteFileInfos);

        return $errors;
    }
}
