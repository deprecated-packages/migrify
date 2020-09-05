<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\Collector;

use Migrify\StaticDetector\Exception\ShouldNotHappenException;
use Migrify\StaticDetector\ValueObject\StaticClassMethod;
use Migrify\StaticDetector\ValueObject\StaticClassMethodWithStaticCalls;
use Migrify\StaticDetector\ValueObject\StaticReport;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
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

    public function addStaticCall(StaticCall $staticCall, ?ClassLike $classLike = null): void
    {
        if ($staticCall->class instanceof Expr) {
            // weird expression, skip
            return;
        }

        $class = $this->resolveClass($staticCall->class, $classLike);

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

            $staticClassMethodWithStaticCalls[] = new StaticClassMethodWithStaticCalls(
                $staticClassMethod,
                $staticCalls
            );
        }

        return $staticClassMethodWithStaticCalls;
    }

    private function resolveClass(Name $staticClassName, ?ClassLike $classLike = null): string
    {
        $class = (string) $staticClassName;
        if (in_array($class, ['self', 'static'], true)) {
            if ($classLike === null) {
                throw new ShouldNotHappenException();
            }

            return (string) $classLike->namespacedName;
        }

        if ($class === 'parent') {
            if (! $classLike instanceof Class_) {
                throw new ShouldNotHappenException();
            }

            if ($classLike->extends === null) {
                throw new ShouldNotHappenException();
            }

            return (string) $classLike->extends;
        }

        return $class;
    }
}
