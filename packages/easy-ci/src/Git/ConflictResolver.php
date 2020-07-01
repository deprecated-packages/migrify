<?php

declare(strict_types=1);

namespace Migrify\EasyCI\Git;

use Migrify\EasyCI\Finder\FileFinder;
use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Migrify\EasyCI\Tests\Git\ConflictResolver\ConflictResolverTest
 */
final class ConflictResolver
{
    /**
     * @var FileFinder
     */
    private $fileFinder;

    public function __construct(FileFinder $fileFinder)
    {
        $this->fileFinder = $fileFinder;
    }

    /**
     * @param string[] $source
     */
    public function extractFromSource(array $source): array
    {
        $fileInfos = $this->fileFinder->findInDirectories($source);
        return $this->extractFromFileInfos($fileInfos);
    }

    public function extractFromFileInfo(SmartFileInfo $fileInfo): int
    {
        $conflictsMatch = Strings::matchAll($fileInfo->getContents(), '#^<<<<<<<<#');

        return count($conflictsMatch);
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return int[]
     */
    private function extractFromFileInfos(array $fileInfos)
    {
        $conflictCountsByFilePath = [];

        foreach ($fileInfos as $fileInfo) {
            $conflictCount = $this->extractFromFileInfo($fileInfo);
            if ($conflictCount === 0) {
                continue;
            }

            $conflictCountsByFilePath[$fileInfo->getRelativeFilePathFromCwd()] = $conflictCount;
        }

        return $conflictCountsByFilePath;
    }
}
