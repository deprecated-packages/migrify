<?php

declare(strict_types=1);

namespace Migrify\ConfigClarity\Tests;

use Iterator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Mostly copied from
 * @see https://github.com/rectorphp/rector/blob/master/src/Testing/StaticFixtureProvider.php
 */
final class StaticFixtureProvider
{
    public static function yieldFilesFromDirectory(string $directory): Iterator
    {
        $fileInfos = self::findFilesInDirectory($directory);

        foreach ($fileInfos as $fileInfo) {
            yield [$fileInfo->getPathName()];
        }
    }

    /**
     * @return SplFileInfo[]
     */
    private static function findFilesInDirectory(string $directory): array
    {
        $finder = Finder::create()->in($directory)->files();

        $fileInfos = iterator_to_array($finder);

        return array_values($fileInfos);
    }
}
