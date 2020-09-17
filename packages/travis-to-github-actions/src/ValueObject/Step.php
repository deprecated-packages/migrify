<?php

declare(strict_types=1);

namespace Migrify\TravisToGithubActions\ValueObject;

final class Step
{
    /**
     * @var string
     */
    private $uses;

    /**
     * @var string[]
     */
    private $withs = [];

    /**
     * @param string[] $withs
     */
    public function __construct(string $uses, array $withs = [])
    {
        $this->uses = $uses;
        $this->withs = $withs;
    }

    public function getUses(): string
    {
        return $this->uses;
    }

    /**
     * @return string[]
     */
    public function getWiths(): array
    {
        return $this->withs;
    }
}
