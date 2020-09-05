<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\ValueObject;

final class StaticReport
{
    /**
     * @var int
     */
    private $staticCallsCount;

    /**
     * @var StaticClassMethodWithStaticCalls[]
     */
    private $staticClassMethodsWithStaticCalls = [];

    /**
     * @param StaticClassMethodWithStaticCalls[] $staticClassMethodsWithStaticCalls
     */
    public function __construct(array $staticClassMethodsWithStaticCalls)
    {
        $staticCallsCount = 0;
        foreach ($staticClassMethodsWithStaticCalls as $staticClassMethodWithStaticCalls) {
            $staticCallsCount += count($staticClassMethodWithStaticCalls->getStaticCalls());
        }

        $this->staticCallsCount = $staticCallsCount;

        $this->staticClassMethodsWithStaticCalls = $staticClassMethodsWithStaticCalls;
    }

    /**
     * @return StaticClassMethodWithStaticCalls[]
     */
    public function getStaticClassMethodsWithStaticCalls(): array
    {
        return $this->staticClassMethodsWithStaticCalls;
    }

    public function getStaticCallsCount(): int
    {
        return $this->staticCallsCount;
    }

    public function getStaticClassMethodCount(): int
    {
        return count($this->staticClassMethodsWithStaticCalls);
    }
}
