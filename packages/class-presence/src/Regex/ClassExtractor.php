<?php

declare(strict_types=1);

namespace Migrify\ClassPresence\Regex;

use Nette\Neon\Neon;
use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassExtractor
{
    /**
     * @var string
     * @see https://regex101.com/r/1VKOxi/3/
     */
    private const CLASS_NAME_REGEX = '#\b(?<class_name>[A-Z](\w+\\\\(\\\\)?)+(\w+))(::)?\b(?<next_char>\\\\)?#';

    /**
     * @return string[]
     */
    public function extractFromFileInfo(SmartFileInfo $fileInfo): array
    {
        $classNames = [];

        $fileContent = $this->getFileContent($fileInfo);

        $matches = Strings::matchAll($fileContent, self::CLASS_NAME_REGEX);
        foreach ($matches as $match) {
            if (isset($match['next_char']) && $match['next_char'] === '\\') {
                // is Symfony autodiscovery → skip
                continue;
            }

            $classNames[] = $match['class_name'];
        }

        return $classNames;
    }

    private function getFileContent(SmartFileInfo $fileInfo): string
    {
        if (Strings::match($fileInfo->getRealPath(), '#\.neon$#')) {
            $neon = Neon::decode($fileInfo->getContents());

            // section with no classes that resemble classes
            unset($neon['mapping']);

            return Neon::encode($neon);
        }

        return $fileInfo->getContents();
    }
}
