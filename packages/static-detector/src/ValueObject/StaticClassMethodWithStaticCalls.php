<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\ValueObject;

use PhpParser\Node;
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
    private $staticCalls = [];

    /**
     * @var string[]
     */
    private $staticCallsFilePathsWithLines = [];

    /**
     * @param StaticCall[] $staticCalls
     */
    public function __construct(StaticClassMethod $staticClassMethod, array $staticCalls)
    {
        $this->staticClassMethod = $staticClassMethod;
        $this->staticCalls = $staticCalls;
        $this->staticCallsFilePathsWithLines = $this->createFilePathsWithLinesFromNodes($staticCalls);
    }

    public function getStaticClassMethodName(): string
    {
        return $this->staticClassMethod->getClass() . '::' . $this->staticClassMethod->getMethod();
    }

    /**
     * @return StaticCall[]
     */
    public function getStaticCalls(): array
    {
        return $this->staticCalls;
    }

    public function getStaticCallFileLocationWithLine(): string
    {
        return $this->staticClassMethod->getFileLocationWithLine();
    }

    /**
     * @return string[]
     */
    public function getStaticCallsFilePathsWithLines(): array
    {
        return $this->staticCallsFilePathsWithLines;
    }

    /**
     * @param Node[] $staticCalls
     * @return string[]
     */
    private function createFilePathsWithLinesFromNodes(array $staticCalls): array
    {
        $nodes = [];
        foreach ($staticCalls as $node) {
            $nodes[] = $node->getAttribute(StaticDetectorAttributeKey::FILE_LINE);
        }

        return $nodes;
    }
}
