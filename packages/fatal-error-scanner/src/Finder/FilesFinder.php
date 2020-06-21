<?php

declare(strict_types=1);

namespace Migrify\FatalErrorScanner\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\FileSystemFilter;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FilesFinder
{
    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    /**
     * @var FileSystemFilter
     */
    private $fileSystemFilter;

    public function __construct(FinderSanitizer $finderSanitizer, FileSystemFilter $fileSystemFilter)
    {
        $this->finderSanitizer = $finderSanitizer;
        $this->fileSystemFilter = $fileSystemFilter;
    }

    /**
     * @param string[] $source
     * @param string[] $suffixes
     * @return SmartFileInfo[]
     */
    public function findInDirectoriesAndFiles(array $source, array $suffixes = ['*.php']): array
    {
        $files = $this->fileSystemFilter->filterFiles($source);
        $directories = $this->fileSystemFilter->filterDirectories($source);

        $smartFileInfos = [];
        foreach ($files as $file) {
            $smartFileInfos[] = new SmartFileInfo($file);
        }

        return array_merge($smartFileInfos, $this->findInDirectories($directories, $suffixes));
    }

    /**
     * @param string[] $directories
     * @param string[] $suffixes
     * @return SmartFileInfo[]
     */
    private function findInDirectories(array $directories, array $suffixes): array
    {
        if (count($directories) === 0) {
            return [];
        }

        $suffixesPattern = $this->normalizeSuffixesToPattern($suffixes);

        $finder = Finder::create()
            ->followLinks()
            ->files()
            ->in($directories)
            ->name($suffixesPattern)
            ->sortByName();

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @param string[] $suffixes
     */
    private function normalizeSuffixesToPattern(array $suffixes): string
    {
        $suffixesPattern = implode('|', $suffixes);

        return '#\.(' . $suffixesPattern . ')$#';
    }
}
