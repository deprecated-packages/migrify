<?php

declare(strict_types=1);

namespace Migrify\TravisToGithubActions\Printer;

use Migrify\TravisToGithubActions\ValueObject\GithubActions;
use Symfony\Component\Yaml\Yaml;

final class GithubActionsToYAMLPrinter
{
    public function print(GithubActions $githubActions): string
    {
        $data = [];

        foreach ($githubActions->getJobs() as $job) {
            $jobSteps = [];
            foreach ($job->getSteps() as $jobStep) {
                $singleStep = [
                    'uses' => $jobStep->getUses(),
                ];

                if ($jobStep->getWiths() !== []) {
                    $singleStep['with'] = $jobStep->getWiths();
                }

                $jobSteps[] = $singleStep;
            }

            $data['jobs'][$job->getName()] = [
                'runs-on' => $job->getRunsOn(),
                'steps' => $jobSteps,
            ];
        }

        return Yaml::dump($data, 100);
    }
}
