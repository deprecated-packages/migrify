<?php

declare(strict_types=1);

namespace Migrify\CIToGithubActions\ValueObject;

final class Step
{
    /**
     * @var string
     */
    private $uses;

    public function __construct(string $uses)
    {
        $this->uses = $uses;
    }

    public function getUses(): string
    {
        return $this->uses;
    }
}
