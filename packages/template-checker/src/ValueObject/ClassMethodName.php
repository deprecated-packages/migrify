<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker\ValueObject;

use ReflectionMethod;
use Symplify\SmartFileSystem\SmartFileInfo;
use function ucfirst;

final class ClassMethodName
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
     * @var SmartFileInfo
     */
    private $latteFileInfo;

    public function __construct(string $class, string $method, SmartFileInfo $latteFileInfo)
    {
        $this->class = $class;
        $this->method = $method;
        $this->latteFileInfo = $latteFileInfo;
    }

    public function getClassMethodName(): string
    {
        return $this->class . '::' . $this->method;
    }

    public function getFileLine(): string
    {
        $reflectionMethod = $this->getReflectionMethod();
        return $reflectionMethod->getFileName() . ':' . $reflectionMethod->getStartLine();
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getFilterProviderClassName(): string
    {
        return ucfirst($this->method) . 'FilterProvider';
    }

    public function getReflectionMethod(): ReflectionMethod
    {
        return new ReflectionMethod($this->class, $this->method);
    }

    public function getLatteFilePath(): string
    {
        return $this->latteFileInfo->getPathname();
    }
}
