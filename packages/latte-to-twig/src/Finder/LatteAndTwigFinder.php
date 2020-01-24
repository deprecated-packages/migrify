<?php

declare(strict_types=1);

namespace Migrify\LatteToTwig\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LatteAndTwigFinder
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
     * @return SmartFileInfo[]
     */
    public function findTwigAndLatteFilesInSource(string $source): array
    {
        if (is_file($source) && file_exists($source)) {
            return [new SmartFileInfo($source)];
        }

        $finder = Finder::create()
            ->files()
            ->in($source)
            ->name('#\.(twig|latte)$#')
            ->sortByName();

        return $this->finderSanitizer->sanitize($finder);
    }
}
