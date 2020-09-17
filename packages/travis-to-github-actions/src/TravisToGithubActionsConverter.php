<?php

declare(strict_types=1);

namespace Migrify\TravisToGithubActions;

use Migrify\MigrifyKernel\Exception\NotImplementedYetException;
use Migrify\TravisToGithubActions\Printer\GithubActionsToYAMLPrinter;
use Migrify\TravisToGithubActions\ValueObject\GithubActions;
use Migrify\TravisToGithubActions\ValueObject\Job;
use Migrify\TravisToGithubActions\ValueObject\Step;
use Symfony\Component\Yaml\Yaml;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Migrify\TravisToGithubActions\Tests\TravisToGithubActionsConverter\TravisToGithubActionsConverterTest
 */
final class TravisToGithubActionsConverter
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

        $githubActions = $this->createGithubActions($yaml);

        return $this->githubActionsToYAMLPrinter->print($githubActions);
    }

    /**
     * @param mixed[] $yaml
     */
    private function createGithubActions(array $yaml): GithubActions
    {
        // now we know that checkout + PHP version actions are needed
        $jobs = [];
        $steps = [new Step('actions/checkout@v2')];

        $phpVersions = (array) ($yaml['php'] ?? []);
        if (count($phpVersions) === 1) {
            $withs = [
                'php-version' => $phpVersions[0],
            ];

            $steps[] = new Step('shivammathur/setup-php@v2.5', $withs);
        } else {
            $steps[] = new Step('shivammathur/setup-php@v2.5');
        }

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
