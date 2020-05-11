<?php

declare(strict_types=1);

namespace Migrify\ConfigClassPresence\Regex;

use Symplify\SmartFileSystem\SmartFileInfo;

final class NonExistingClassExtractor
{
    /**
     * @var ClassExtractor
     */
    private $classExtractor;

    public function __construct(ClassExtractor $classExtractor)
    {
        $this->classExtractor = $classExtractor;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return string[][]
     */
    public function extractFromFileInfos(array $fileInfos): array
    {
        $nonExistingClassesByFile = [];
        foreach ($fileInfos as $fileInfo) {
            $classes = $this->classExtractor->extractFromFileInfo($fileInfo);

            $nonExistingClasses = $this->filterNonExistingClasses($classes);
            if ($nonExistingClasses === []) {
                continue;
            }

            sort($nonExistingClasses);
            $nonExistingClassesByFile[$fileInfo->getRelativeFilePathFromCwd()] = $nonExistingClasses;
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
