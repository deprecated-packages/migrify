<?php

declare(strict_types=1);

namespace Migrify\EasyCI\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

final class SrcAndTestsDirectories
{
    /**
     * @var SmartFileInfo[]
     */
    private $srcDirectories = [];

    /**
     * @var SmartFileInfo[]
     */
    private $testsDirectories = [];

    /**
     * @param SmartFileInfo[] $srcDirectories
     * @param SmartFileInfo[] $testsDirectories
     */
    public function __construct(array $srcDirectories, array $testsDirectories)
    {
        $this->srcDirectories = $srcDirectories;
        $this->testsDirectories = $testsDirectories;
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getSrcDirectories(): array
    {
        return $this->srcDirectories;
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getTestsDirectories(): array
    {
        return $this->testsDirectories;
    }
}
