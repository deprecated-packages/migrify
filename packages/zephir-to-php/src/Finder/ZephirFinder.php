<?php

declare(strict_types=1);

namespace Migrify\ZephirToPhp\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ZephirFinder
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
        $finder = Finder::create()
            ->files()
            ->in($directories)
            ->name('#\.(zep)$#')
            ->sortByName();

        return $this->finderSanitizer->sanitize($finder);
    }
}
