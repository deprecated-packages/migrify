<?php

declare(strict_types=1);

namespace Migrify\TravisToGithubActions\ValueObject;

use Webmozart\Assert\Assert;

final class Job
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Step[]
     */
    private $steps = [];

    /**
     * @var string
     */
    private $runsOn;

    /**
     * @param Step[] $steps
     */
    public function __construct(string $name, array $steps, string $runsOn = 'ubuntu-latest')
    {
        $this->name = $name;
        Assert::allIsInstanceOf($steps, Step::class);
        $this->steps = $steps;
        $this->runsOn = $runsOn;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Step[]
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    public function getRunsOn(): string
    {
        return $this->runsOn;
    }
}
