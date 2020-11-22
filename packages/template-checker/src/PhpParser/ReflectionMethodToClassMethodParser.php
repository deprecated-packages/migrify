<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker\PhpParser;

use Migrify\MigrifyKernel\Exception\ShouldNotHappenException;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\Parser;
use ReflectionMethod;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ReflectionMethodToClassMethodParser
{
    /**
     * @var Parser
     */
    private $phpParser;

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(Parser $phpParser, NodeFinder $nodeFinder, SmartFileSystem $smartFileSystem)
    {
        $this->phpParser = $phpParser;
        $this->nodeFinder = $nodeFinder;
        $this->smartFileSystem = $smartFileSystem;
    }

    public function parse(ReflectionMethod $reflectionMethod): ClassMethod
    {
        $desiredMethodName = $reflectionMethod->name;

        $fileName = $reflectionMethod->getFileName();
        if ($fileName === false) {
            throw new ShouldNotHappenException();
        }

        $reflectionMethodFileContent = $this->smartFileSystem->readFile($fileName);
        $nodes = $this->phpParser->parse($reflectionMethodFileContent);
        if ($nodes === [] || $nodes === null) {
            throw new ShouldNotHappenException();
        }

        /** @var ClassMethod|null $classMethod */
        $classMethod = $this->nodeFinder->findFirst($nodes, static function (Node $node) use (
            $desiredMethodName
        ): bool {
            if (! $node instanceof ClassMethod) {
                return false;
            }

            return (string) $node->name === $desiredMethodName;
        });

        if ($classMethod === null) {
            $name = $reflectionMethod->getDeclaringClass()
                ->getName();
            $errorMessage = sprintf('Method "%s" could not found in "%s" class', $desiredMethodName, $name);
            throw new ShouldNotHappenException($errorMessage);
        }

        return $classMethod;
    }
}
