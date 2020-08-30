<?php

declare(strict_types=1);

namespace Migrify\DiffDataMiner\ValueObject;

final class ClassBeforeAndClassAfter
{
    /**
     * @var string
     */
    private $classBefore;

    /**
     * @var string
     */
    private $classAfter;

    public function __construct(string $classBefore, string $classAfter)
    {
        $this->classBefore = $classBefore;
        $this->classAfter = $classAfter;
    }

    public function getClassBefore(): string
    {
        return $this->classBefore;
    }

    public function getClassAfter(): string
    {
        return $this->classAfter;
    }

    public function areIdentical(): bool
    {
        return $this->classBefore === $this->classAfter;
    }
}
