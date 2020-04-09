<?php

declare(strict_types=1);

namespace Migrify\Psr4Switcher\Finder;

use Migrify\Psr4Switcher\RobotLoader\PhpClassLoader;

final class MultipleClassInOneFileFinder
{
    /**
     * @var PhpClassLoader
     */
    private $phpClassLoader;

    public function __construct(PhpClassLoader $phpClassLoader)
    {
        $this->phpClassLoader = $phpClassLoader;
    }

    /**
     * @return string[][]
     */
    public function findInDirectories(array $directories): array
    {
        $fileByClasses = $this->phpClassLoader->load($directories);

        $classesByFile = [];
        foreach ($fileByClasses as $class => $file) {
            $classesByFile[$file][] = $class;
        }

        return array_filter($classesByFile, function ($classes) {
            return count($classes) >= 2;
        });
    }
}
