<?php

declare(strict_types=1);

namespace Migrify\DiffDataMiner\Extractor;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

final class ClassChangesExtractor
{
    /**
     * @todo modify
     * @var string
     */
    private const CLASS_NAME_PREFIX = 'Doctrine';

    /**
     * @see https://regex101.com/r/tyABnc/1/
     * @var string
     */
    private const BEFORE_AFTER_PATTERN = '#^\-(?<before>[^-].*?)$\n\+(?<after>.*?)$#ms';

    /**
     * @see https://regex101.com/r/tyABnc/2/
     * @var string
     */
    private const CLASS_NAME_PATTERN = '#(?<class_name>' . self::CLASS_NAME_PREFIX . '\\\\[\w|\\\\]+)#';

    /**
     * @return string[]
     */
    public function extract(string $diffFilePath): array
    {
        $diff = FileSystem::read($diffFilePath);
        $beforeAfterMatches = Strings::matchAll($diff, self::BEFORE_AFTER_PATTERN);

        $classesBeforeAfter = [];
        foreach ($beforeAfterMatches as $beforeAfterMatch) {
            $classBeforeAndAfter = $this->resolveClassBeforeAndAfter($beforeAfterMatch);
            if ($classBeforeAndAfter === null) {
                continue;
            }

            [$classBefore, $classAfter] = $classBeforeAndAfter;

            if (Strings::contains($classBefore, 'Tests')) {
                continue;
            }

            // classes are the same, no change in the class name
            if ($classBefore === $classAfter) {
                continue;
            }

            $classesBeforeAfter[$classBefore] = $classAfter;
        }

        ksort($classesBeforeAfter);

        return $classesBeforeAfter;
    }

    /**
     * @return string[]|null
     */
    private function resolveClassBeforeAndAfter(array $beforeAfterMatch): ?array
    {
        // file change
        if (Strings::contains($beforeAfterMatch['before'], '//')) {
            return null;
        }

        $classNameBefore = Strings::match($beforeAfterMatch['before'], self::CLASS_NAME_PATTERN);
        if ($classNameBefore === null) {
            return null;
        }

        $classNameAfter = Strings::match($beforeAfterMatch['after'], self::CLASS_NAME_PATTERN);
        if ($classNameAfter === null) {
            return null;
        }

        return [$classNameBefore['class_name'], $classNameAfter['class_name']];
    }
}
