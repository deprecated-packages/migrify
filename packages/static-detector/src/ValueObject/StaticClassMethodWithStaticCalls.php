<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\ValueObject;

use PhpParser\Node\Expr\StaticCall;

final class StaticClassMethodWithStaticCalls
{
    /**
     * @var StaticClassMethod
     */
    private $staticClassMethod;

    /**
     * @var StaticCall[]
     */
    private $staticCalls;

    /**
     * @param StaticCall[] $staticCalls
     */
    public function __construct(StaticClassMethod $staticClassMethod, array $staticCalls)
    {
        $this->staticClassMethod = $staticClassMethod;
        $this->staticCalls = $staticCalls;
    }

    public function getStaticClassMethod(): StaticClassMethod
    {
        return $this->staticClassMethod;
    }

    /**
     * @return StaticCall[]
     */
    public function getStaticCalls(): array
    {
        return $this->staticCalls;
    }
}
