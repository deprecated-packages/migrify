<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker\Analyzer;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;
use function class_exists;
use function interface_exists;
use function trait_exists;

/**
 * @see \Migrify\TemplateChecker\Tests\Analyzer\MissingClassesLatteAnalyzer\MissingClassesLatteAnalyzerTest
 */
final class MissingClassesLatteAnalyzer
{
    /**
     * @see https://regex101.com/r/Wrfff2/7
     * @var string
     */
    private const CLASS_PATTERN = '#\b(?<class>[A-Z][\w\\\\]+)::#m';

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return string[]
     */
    public function analyze(array $fileInfos): array
    {
        $errors = [];

        foreach ($fileInfos as $fileInfo) {
            $matches = Strings::matchAll($fileInfo->getContents(), self::CLASS_PATTERN);
            if ($matches === []) {
                continue;
            }

            foreach ($matches as $foundClassesMatch) {
                $class = $foundClassesMatch['class'];
                if (class_exists($class) || trait_exists($class) || interface_exists($class)) {
                    continue;
                }

                $errors[] = sprintf(
                    'Class "%s" was not found in "%s"',
                    $class,
                    $fileInfo->getRelativeFilePathFromCwd()
                );
            }
        }

        return $errors;
    }
}
