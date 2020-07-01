<?php

declare(strict_types=1);

namespace Migrify\EasyCI\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FileFinder
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
     * @param string[] $directories
     * @return SmartFileInfo[]
     */
    public function findInDirectories(array $directories): array
    {
        $finder = (new Finder())
            ->in($directories)
            ->files()
            ->notPath('vendor')
            ->notPath('tests');

        return $this->finderSanitizer->sanitize($finder);
    }
}
