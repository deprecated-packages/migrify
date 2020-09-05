<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\ValueObject;

use Migrify\StaticDetector\Exception\ShouldNotHappenException;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\SmartFileSystem\SmartFileInfo;

final class StaticClassMethod
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $method;

    /**
     * @var ClassMethod
     */
    private $classMethod;

    public function __construct(string $class, string $method, ClassMethod $classMethod)
    {
        $this->class = $class;
        $this->method = $method;
        $this->classMethod = $classMethod;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getFileLocationWithLine(): string
    {
        /** @var SmartFileInfo|null $fileInfo */
        $fileInfo = $this->classMethod->getAttribute(AttributeKey::FILE_INFO);
        if ($fileInfo === null) {
            throw new ShouldNotHappenException();
        }

        return $fileInfo->getRelativeFilePathFromCwd() . ':' . $this->classMethod->getStartLine();
    }
}
