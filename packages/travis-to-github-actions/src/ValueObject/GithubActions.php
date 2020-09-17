<?php

declare(strict_types=1);

namespace Migrify\TravisToGithubActions\ValueObject;

use Webmozart\Assert\Assert;

final class GithubActions
{
    /**
     * @var Job[]
     */
    private $jobs = [];

    /**
     * @param Job[] $jobs
     */
    public function __construct(array $jobs)
    {
        Assert::allIsInstanceOf($jobs, Job::class);
        $this->jobs = $jobs;
    }

    /**
     * @return Job[]
     */
    public function getJobs(): array
    {
        return $this->jobs;
    }
}
