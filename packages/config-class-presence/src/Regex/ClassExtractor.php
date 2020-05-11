<?php

declare(strict_types=1);

namespace Migrify\ConfigClassPresence\Regex;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Migrify\ConfigClassPresence\Tests\Regex\ClassExtractor\ClassExtractorTest
 */
final class ClassExtractor
{
    /**
     * @var string
     * @see https://regex101.com/r/1VKOxi/2/
     */
    private const CLASS_NAME_PATTERN = '#\b(?<class_name>[A-Z](\w+\\\\)[\w\\\\]+)\b#';

    /**
     * @return string[]
     */
    public function extractFromFileInfo(SmartFileInfo $fileInfo): array
    {
        $classNames = [];

        $matches = Strings::matchAll($fileInfo->getContents(), self::CLASS_NAME_PATTERN);
        foreach ($matches as $match) {
            $classNames[] = $match['class_name'];
        }

        return $classNames;
    }
}
