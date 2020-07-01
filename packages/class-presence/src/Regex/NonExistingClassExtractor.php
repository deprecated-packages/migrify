<?php

declare(strict_types=1);

namespace Migrify\ClassPresence\Regex;

use Migrify\ClassPresence\Finder\FileFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Migrify\ClassPresence\Tests\Regex\NonExistingClassExtractor\NonExistingClassExtractorTest
 */
final class NonExistingClassExtractor
{
    /**
     * @var ClassExtractor
     */
    private $classExtractor;

    /**
     * @var FileFinder
     */
    private $fileFinder;

    public function __construct(FileFinder $configFinder, ClassExtractor $classExtractor)
    {
        $this->fileFinder = $configFinder;
        $this->classExtractor = $classExtractor;
    }

    /**
     * @param string[] $source
     * @return string[][]
     */
    public function extractFromSource(array $source): array
    {
        $fileInfos = $this->fileFinder->findIn($source);
        return $this->extractFromFileInfos($fileInfos);
    }

    /**
     * @return string[]
     */
    public function extractFromFileInfo(SmartFileInfo $fileInfo): array
    {
        $classes = $this->classExtractor->extractFromFileInfo($fileInfo);
        $nonExistingClasses = $this->filterNonExistingClasses($classes);
        if ($nonExistingClasses === []) {
            return [];
        }

        sort($nonExistingClasses);

        return $nonExistingClasses;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return string[][]
     */
    private function extractFromFileInfos(array $fileInfos): array
    {
        $nonExistingClassesByFile = [];
        foreach ($fileInfos as $fileInfo) {
            $nonExistingClassesByFile[$fileInfo->getRelativeFilePathFromCwd()] = $this->extractFromFileInfo($fileInfo);
        }

        return $nonExistingClassesByFile;
    }

    /**
     * @param string[] $classes
     * @return string[]
     */
    private function filterNonExistingClasses(array $classes): array
    {
        return array_filter($classes, function (string $class) {
            return ! $this->doesClassExists($class);
        });
    }

    private function doesClassExists(string $className): bool
    {
        if (class_exists($className)) {
            return true;
        }

        if (interface_exists($className)) {
            return true;
        }

        return trait_exists($className);
    }
}
