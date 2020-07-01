<?php

declare(strict_types=1);

namespace Migrify\ClassPresence\Regex;

use Migrify\ClassPresence\Finder\FileFinder;
use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Migrify\ClassPresence\Tests\Regex\NonExistingClassConstantExtractor\NonExistingClassConstantExtractorTest
 */
final class NonExistingClassConstantExtractor
{
    /**
     * @var string
     * @see https://regex101.com/r/Wrfff2/1
     */
    private const CLASS_CONSTANT_NAME_PATTERN = '#\b(?<class_constant_name>[A-Z](\w+\\\\(\\\\)?)+(\w+)::[A-Z_]+)#';

    /**
     * @var FileFinder
     */
    private $fileFinder;

    public function __construct(FileFinder $fileFinder)
    {
        $this->fileFinder = $fileFinder;
    }

    /**
     * @return string[]
     */
    public function extractFromFileInfo(SmartFileInfo $fileInfo): array
    {
        $foundMatches = Strings::matchAll($fileInfo->getContents(), self::CLASS_CONSTANT_NAME_PATTERN);
        if ($foundMatches === []) {
            return [];
        }

        $missingClassConstantNames = [];
        foreach ($foundMatches as $foundMatch) {
            $classConstantName = $foundMatch['class_constant_name'];
            if (defined($classConstantName)) {
                continue;
            }

            $missingClassConstantNames[] = $classConstantName;
        }

        return $missingClassConstantNames;
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
     * @param SmartFileInfo[] $fileInfos
     * @return string[][]
     */
    private function extractFromFileInfos(array $fileInfos): array
    {
        $missingClassConstantsByFilePath = [];

        foreach ($fileInfos as $fileInfo) {
            $missingClassConstants = $this->extractFromFileInfo($fileInfo);
            if ($missingClassConstants === []) {
                continue;
            }

            $missingClassConstantsByFilePath[$fileInfo->getRelativePathname()] = $missingClassConstants;
        }

        return $missingClassConstantsByFilePath;
    }
}
