<?php

declare(strict_types=1);

namespace Migrify\DiffDataMiner\Extractor;

use Migrify\DiffDataMiner\Exception\ShouldNotHappenException;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

final class DefaultValueChangesExtractor
{
    /**
     * @see https://regex101.com/r/pe3DNc/1/
     * @var string
     */
    private const FILE_PATTERN = '#^--- a\/src\/(?<file_name>(.*?))\.php$#ms';

    /**
     * @see https://regex101.com/r/pe3DNc/1/
     * @var string
     */
    private const REMOVED_DEFAULT_VALUE_PATTERN = '#^-(.*?)function\s(?<method_name>\w+)(.*?)=\s?(?<value>.*?)\)$#ms';

    /**
     * @var string[]
     */
    private const CLASSES_TO_SKIP = [
        'PhpOffice\PhpSpreadsheet\Shared\JAMA\utils\Error',
        'PhpOffice\PhpSpreadsheet\Worksheet\Dimension',
    ];

    /**
     * @var string|null
     */
    private $currentClass;

    /**
     * @var mixed[]
     */
    private $collectedChanges = [];

    /**
     * @var string[]
     */
    private $newToOldClasses = [];

    /**
     * @return string[]
     */
    public function extract(string $diffFilepath): array
    {
        $lineByLineContent = $this->readFileToLines($diffFilepath);

        foreach ($lineByLineContent as $lineContent) {
            $this->detectCurrentClass($lineContent);

            if (in_array($this->currentClass, self::CLASSES_TO_SKIP, true)) {
                continue;
            }

            $matches = Strings::match($lineContent, self::REMOVED_DEFAULT_VALUE_PATTERN);
            if (! $matches) {
                continue;
            }

            // match args
            $match = Strings::match($lineContent, '#\((?<parameters>.*?)\)#');
            if (! isset($match['parameters'])) {
                throw new ShouldNotHappenException();
            }

            $methodName = $matches['method_name'];
            // $value = $matches['value'];

            $parameters = Strings::split($match['parameters'], '#,\s+#');
            foreach ($parameters as $position => $parameterContent) {
                $value = $this->resolveValue($parameterContent, $lineContent);

                $this->collectedChanges[$this->currentClass][$methodName][$position] = $value;
            }
        }

        ksort($this->collectedChanges);

        return $this->collectedChanges;
    }

    private function detectCurrentClass(string $fileContent): void
    {
        $matches = Strings::match($fileContent, self::FILE_PATTERN);
        if (! $matches) {
            return;
        }

        // turn file into class
        $class = Strings::replace($matches['file_name'], '#/#', '\\');
        $newClass = 'PhpOffice\\' . $class;

        $isClassSkipped = in_array($newClass, self::CLASSES_TO_SKIP, true);
        if (! $isClassSkipped && ! isset($this->newToOldClasses[$newClass])) {
            throw new ShouldNotHappenException(sprintf('Could not find old class for "%s"', $newClass));
        }

        $oldClass = $this->newToOldClasses[$newClass] ?? $newClass;
        $this->currentClass = $oldClass;
    }

    /**
     * @return string[]
     */
    private function readFileToLines(string $diffFilepath): array
    {
        $fileContent = FileSystem::read($diffFilepath);
        return explode(PHP_EOL, $fileContent);
    }

    private function resolveValue(string $parameterContent, string $lineContent)
    {
        $match = Strings::match($parameterContent, '#=\s+(?<default_value>.*?)$#');
        if ($match === null) {
            throw new ShouldNotHappenException(sprintf('Line: %d , content: %s', $lineContent, $parameterContent));
        }

        $value = $match['default_value'];
        if ($value === 'null') {
            return null;
        }

        if ($value === 'false') {
            return false;
        }

        if ($value === 'true') {
            return true;
        }

        return $value;
    }
}
