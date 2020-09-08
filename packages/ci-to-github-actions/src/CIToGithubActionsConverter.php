<?php

declare(strict_types=1);

namespace Migrify\CIToGithubActions;

use Migrify\CIToGithubActions\Printer\GithubActionsToYAMLPrinter;
use Migrify\CIToGithubActions\ValueObject\GithubActions;
use Migrify\CIToGithubActions\ValueObject\Job;
use Migrify\CIToGithubActions\ValueObject\Step;
use Migrify\MigrifyKernel\Exception\NotImplementedYetException;
use Symfony\Component\Yaml\Yaml;
use Symplify\SmartFileSystem\SmartFileInfo;

final class CIToGithubActionsConverter
{
    /**
     * @var GithubActionsToYAMLPrinter
     */
    private $githubActionsToYAMLPrinter;

    public function __construct(GithubActionsToYAMLPrinter $githubActionsToYAMLPrinter)
    {
        $this->githubActionsToYAMLPrinter = $githubActionsToYAMLPrinter;
    }

    public function convert(SmartFileInfo $fileInfo): string
    {
        // only work with ".travis.yml" format now
        $yaml = Yaml::parse($fileInfo->getContents());

        $this->ensureInputIsPHPTravis($yaml);

        $githubActions = $this->createGithubActions();

        return $this->githubActionsToYAMLPrinter->print($githubActions);
    }

    private function createGithubActions(): GithubActions
    {
        // now we know that checkout + PHP version actions are needed
        $jobs = [];
        $steps = [new Step('actions/checkout@v2'), new Step('shivammathur/setup-php@v2.5')];

        $jobs[] = new Job('default', $steps);

        return new GithubActions($jobs);
    }

    private function ensureInputIsPHPTravis(array $yaml): void
    {
        $language = $yaml['language'] ?? null;
        if ($language === null) {
            throw new NotImplementedYetException('"language" option is missing');
        }

        if ($language !== 'php') {
            $message = sprintf('Only "php" language is supported. "%s" given', $language);
            throw new NotImplementedYetException($message);
        }
    }
}
