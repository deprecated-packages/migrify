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
     * @return string[]
     */
    public function getRelativePathSrcDirectories(): array
    {
        $relativePaths = [];
        foreach ($this->srcDirectories as $srcDirectoryFileInfo) {
            $relativePaths[] = $srcDirectoryFileInfo->getRelativeFilePathFromCwd();
        }

        sort($relativePaths);

        return $relativePaths;
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getTestsDirectories(): array
    {
        return $this->testsDirectories;
    }

    /**
     * @return string[]
     */
    public function getRelativePathTestsDirectories(): array
    {
        $relativePaths = [];
        foreach ($this->testsDirectories as $testsDirectoryFileInfo) {
            $relativePaths[] = $testsDirectoryFileInfo->getRelativeFilePathFromCwd();
        }

        sort($relativePaths);

        return $relativePaths;
    }
}
