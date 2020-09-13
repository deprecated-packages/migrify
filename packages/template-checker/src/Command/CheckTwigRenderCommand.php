<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker\Command;

use Migrify\TemplateChecker\Finder\GenericFilesFinder;
use Migrify\TemplateChecker\Template\RenderMethodTemplateExtractor;
use Migrify\TemplateChecker\Template\TemplatePathsResolver;
use Migrify\TemplateChecker\ValueObject\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class CheckTwigRenderCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var TemplatePathsResolver
     */
    private $possibleTemplatePathsResolver;

    /**
     * @var RenderMethodTemplateExtractor
     */
    private $renderMethodTemplateExtractor;

    /**
     * @var GenericFilesFinder
     */
    private $genericFilesFinder;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        TemplatePathsResolver $possibleTemplatePathsResolver,
        GenericFilesFinder $genericFilesFinder,
        RenderMethodTemplateExtractor $renderMethodTemplateExtractor
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->possibleTemplatePathsResolver = $possibleTemplatePathsResolver;
        $this->renderMethodTemplateExtractor = $renderMethodTemplateExtractor;
        $this->genericFilesFinder = $genericFilesFinder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Validate template paths in $this->render(...)');
        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to project directories'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument(Option::SOURCES);

        $this->symfonyStyle->title('Analysing controllers and templates');

        $stats = [];

        $controllerFileInfos = $this->genericFilesFinder->find($sources, '#Controller\.php$#');
        $stats[] = sprintf('%d controllers', count($controllerFileInfos));

        $allowedTemplatePaths = $this->possibleTemplatePathsResolver->resolveFromDirectories($sources);
        $stats[] = sprintf('%d twig paths', count($allowedTemplatePaths));

        $usedTemplatePaths = $this->renderMethodTemplateExtractor->extractFromFileInfos($controllerFileInfos);
        $stats[] = sprintf('%d unique used templates in "$this->render()" method', count($usedTemplatePaths));

        $this->symfonyStyle->listing($stats);

        $this->symfonyStyle->newLine(2);

        $errorMessages = [];

        foreach ($usedTemplatePaths as $relativeControllerFilePath => $usedTemplatePaths) {
            foreach ($usedTemplatePaths as $usedTemplatePath) {
                if (in_array($usedTemplatePath, $allowedTemplatePaths, true)) {
                    continue;
                }

                $errorMessages[] = sprintf(
                    'Template reference "%s" used in "%s" controller was not found in existing templates',
                    $usedTemplatePath,
                    $relativeControllerFilePath
                );
            }
        }

        return $this->reportErrorsOrSuccess($errorMessages);
    }

    /**
     * @param string[] $errorMessages
     */
    private function reportErrorsOrSuccess(array $errorMessages): int
    {
        if (count($errorMessages) === 0) {
            $this->symfonyStyle->success('All templates exists');

            return ShellCode::SUCCESS;
        }

        foreach ($errorMessages as $errorMessage) {
            $this->symfonyStyle->note($errorMessage);
        }

        $missingTemplatesMessage = sprintf('Found %d missing templates', count($errorMessages));
        $this->symfonyStyle->error($missingTemplatesMessage);

        return ShellCode::ERROR;
    }
}
