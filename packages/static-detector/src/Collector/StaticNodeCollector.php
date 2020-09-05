<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\Collector;

use Migrify\StaticDetector\ValueObject\StaticClassMethod;
use Migrify\StaticDetector\ValueObject\StaticClassMethodWithStaticCalls;
use Migrify\StaticDetector\ValueObject\StaticReport;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;

final class StaticNodeCollector
{
    /**
     * @var StaticClassMethod[]
     */
    private $staticClassMethods = [];

    /**
     * @var array<string, array<string, StaticCall[]>>
     */
    private $staticCalls = [];

    public function addStaticClassMethod(ClassMethod $classMethod, ClassLike $classLike): void
    {
        $class = (string) $classLike->namespacedName;
        $method = (string) $classMethod->name;

        $this->staticClassMethods[] = new StaticClassMethod($class, $method, $classMethod);
    }

    public function addStaticCall(StaticCall $staticCall): void
    {
        if ($staticCall->class instanceof Expr) {
            // weird expression, skip
            return;
        }

        $class = (string) $staticCall->class;
        if (in_array($class, ['parent', 'self', 'static'], true)) {
            return;
        }

        if ($staticCall->name instanceof Expr) {
            // weird expression, skip
            return;
        }

        $method = (string) $staticCall->name;

        $this->staticCalls[$class][$method][] = $staticCall;
    }

    public function generateStaticReport(): StaticReport
    {
        return new StaticReport($this->getStaticClassMethodWithStaticCalls());
    }

    /**
     * @return StaticClassMethodWithStaticCalls[]
     */
    private function getStaticClassMethodWithStaticCalls(): array
    {
        $staticClassMethodWithStaticCalls = [];

        foreach ($this->staticClassMethods as $staticClassMethod) {
            $staticCalls = $this->staticCalls[$staticClassMethod->getClass()][$staticClassMethod->getMethod()] ?? [];
            if ($staticCalls === []) {
                continue;
            }

            $staticClassMethodWithStaticCalls[] = new StaticClassMethodWithStaticCalls(
                $staticClassMethod,
                $staticCalls
            );
        }

        return $staticClassMethodWithStaticCalls;
    }
}
