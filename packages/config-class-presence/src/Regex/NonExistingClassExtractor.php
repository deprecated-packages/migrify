<?php

declare(strict_types=1);

namespace Migrify\ConfigClassPresence\Regex;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NonExistingClassExtractor
{
    /**
     * @var string
     * @see https://regex101.com/r/1VKOxi/2/
     */
    private const CLASS_NAME_PATTERN = '#\b(?<class_name>[A-Z](\w+\\\\)[\w\\\\]+)\b#';

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return string[][]
     */
    public function extractFromFileInfos(array $fileInfos): array
    {
        $nonExistingClassesByFile = [];
        foreach ($fileInfos as $fileInfo) {
            $classes = $this->extractFromFileInfo($fileInfo);

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
     * @return string[]
     */
    private function extractFromFileInfo(SmartFileInfo $smartFileInfo): array
    {
        $classNames = [];

        $matches = Strings::matchAll($smartFileInfo->getContents(), self::CLASS_NAME_PATTERN);
        foreach ($matches as $match) {
            $classNames[] = $match['class_name'];
        }

        return $classNames;
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
