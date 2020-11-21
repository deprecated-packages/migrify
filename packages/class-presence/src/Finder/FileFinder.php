<?php

declare(strict_types=1);

namespace Migrify\ClassPresence\Finder;

use Migrify\ClassPresence\ValueObject\StaticCheckedFileSuffix;
use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Migrify\ClassPresence\Tests\Finder\ConfigFinder\ConfigFinderTest
 */
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
        $finder = new Finder();
        $finder = $finder->in($directories)
            ->files()
            ->notPath('vendor')
            ->notPath('tests');

        foreach (StaticCheckedFileSuffix::getSuffixesRegex() as $suffixRegex) {
            $finder->name($suffixRegex);
        }

        return $this->finderSanitizer->sanitize($finder);
    }
}
