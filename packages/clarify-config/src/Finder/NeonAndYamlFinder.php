<?php

declare(strict_types=1);

namespace Migrify\ConfigClarity\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NeonAndYamlFinder
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
    public function findYamlAndNeonFilesInSource(string $source): array
    {
        if (is_file($source) && file_exists($source)) {
            return [new SmartFileInfo($source)];
        }

        $finder = Finder::create()
            ->files()
            ->in($source)
            ->name('#\.(yml|yaml|neon)$#i')
            ->sortByName();

        return $this->finderSanitizer->sanitize($finder);
    }
}
