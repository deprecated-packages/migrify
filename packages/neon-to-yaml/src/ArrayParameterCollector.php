<?php

declare(strict_types=1);

namespace Migrify\NeonToYaml;

use Nette\Utils\Strings;
use Symplify\PackageBuilder\Strings\StringFormatConverter;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ArrayParameterCollector
{
    /**
     * @var string[]
     */
    private $parametersToReplace = [];

    /**
     * @var StringFormatConverter
     */
    private $stringFormatConverter;

    public function __construct(StringFormatConverter $stringFormatConverter)
    {
        $this->stringFormatConverter = $stringFormatConverter;
    }

    /**
     * @return string[]
     */
    public function getParametersToReplace(): array
    {
        return $this->parametersToReplace;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
    public function collectFromFiles(array $fileInfos): void
    {
        foreach ($fileInfos as $fileInfo) {
            $content = $fileInfo->getContents();
            $matches = Strings::matchAll($content, '#%(?<param_name>\w+\.\w+)%#');

            foreach ($matches as $match) {
                $oldKey = $match['param_name'];
                $newKey = $this->stringFormatConverter->camelCaseToUnderscore($oldKey);

                $this->parametersToReplace[$oldKey] = $newKey;
            }
        }
    }

    public function matchParameterToReplace(string $key): ?string
    {
        return $this->parametersToReplace[$key] ?? null;
    }
}
