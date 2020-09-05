<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\ValueObject;

use Migrify\StaticDetector\Exception\ShouldNotHappenException;
use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\SmartFileSystem\SmartFileInfo;

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
            $nodes[] = $this->resolveNodeFilePathWithLine($node);
        }

        return $nodes;
    }

    private function resolveNodeFilePathWithLine(Node $node): string
    {
        /** @var SmartFileInfo|null $fileInfo */
        $fileInfo = $node->getAttribute(AttributeKey::FILE_INFO);
        if ($fileInfo === null) {
            throw new ShouldNotHappenException();
        }

        return $fileInfo->getRelativeFilePathFromCwd() . ':' . $node->getStartLine();
    }
}
