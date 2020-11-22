<?php

declare(strict_types=1);

namespace Migrify\DiffDataMiner\Extractor;

use Migrify\DiffDataMiner\ValueObject\ClassBeforeAndClassAfter;
use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileSystem;

/**
 * @see \Migrify\DiffDataMiner\Tests\Extractor\ClassChangesExtractor\ClassChangesExtractorTest
 */
final class ClassChangesExtractor
{
    /**
     * @todo modify if needed
     * @var string
     */
    private const CLASS_NAME_PREFIX = 'Doctrine';

    /**
     * @see https://regex101.com/r/tyABnc/1/
     * @var string
     */
    private const BEFORE_AFTER_REGEX = '#^\-(?<before>[^-].*?)$\n\+(?<after>.*?)$#ms';

    /**
     * @see https://regex101.com/r/tyABnc/2/
     * @var string
     */
    private const CLASS_NAME_REGEX = '#(?<class_name>' . self::CLASS_NAME_PREFIX . '\\\\[\w|\\\\]+)#';

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(SmartFileSystem $smartFileSystem)
    {
        $this->smartFileSystem = $smartFileSystem;
    }

    /**
     * @return array<string, string>
     */
    public function extract(string $diffFilePath): array
    {
        $diff = $this->smartFileSystem->readFile($diffFilePath);
        $beforeAfterMatches = Strings::matchAll($diff, self::BEFORE_AFTER_REGEX);

        $classesBeforeAndAfterAsString = [];
        foreach ($beforeAfterMatches as $beforeAfterMatch) {
            $classBeforeAndAfter = $this->resolveClassBeforeAndAfter($beforeAfterMatch);
            if ($classBeforeAndAfter === null) {
                continue;
            }

            if (Strings::contains($classBeforeAndAfter->getClassBefore(), 'Tests')) {
                continue;
            }

            // classes are the same, no change in the class name
            if ($classBeforeAndAfter->areIdentical()) {
                continue;
            }

            $classesBeforeAndAfterAsString[$classBeforeAndAfter->getClassBefore()] = $classBeforeAndAfter->getClassAfter();
        }

        ksort($classesBeforeAndAfterAsString);

        return $classesBeforeAndAfterAsString;
    }

    private function resolveClassBeforeAndAfter(array $beforeAfterMatch): ?ClassBeforeAndClassAfter
    {
        // file change
        if (Strings::contains($beforeAfterMatch['before'], '//')) {
            return null;
        }

        $classNameBefore = Strings::match($beforeAfterMatch['before'], self::CLASS_NAME_REGEX);
        if ($classNameBefore === null) {
            return null;
        }

        $classNameAfter = Strings::match($beforeAfterMatch['after'], self::CLASS_NAME_REGEX);
        if ($classNameAfter === null) {
            return null;
        }

        return new ClassBeforeAndClassAfter($classNameBefore['class_name'], $classNameAfter['class_name']);
    }
}
