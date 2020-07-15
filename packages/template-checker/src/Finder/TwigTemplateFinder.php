<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class TwigTemplateFinder
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
    public function findInDirectories(array $directories): array
    {
        $finder = new Finder();
        $finder->name('#\.twig#')
            ->in($directories)
            ->exclude('_includes')
            ->files();

        return $this->finderSanitizer->sanitize($finder);
    }
}
