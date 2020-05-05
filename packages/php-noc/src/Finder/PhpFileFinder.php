<?php

declare(strict_types=1);

namespace Migrify\PhpNoc\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PhpFileFinder
{
    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    public function __construct(FinderSanitizer $finderSanitizer)
    {
        $this->finderSanitizer = $finderSanitizer;
    }

    /**
     * @param string[] $source
     * @return SmartFileInfo[]
     */
    public function find(array $source): array
    {
        $finder = new Finder();
        $finder->files()
            ->in($source)
            ->name('*.php');

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @return string[]
     */
    public function getDirectoryCount(array $source): int
    {
        $dirnames = [];
        foreach ($this->find($source) as $fileInfo) {
            $dirnames[] = $fileInfo->getRelativeDirectoryPath();
        }

        return count(array_unique($dirnames));
    }
}
