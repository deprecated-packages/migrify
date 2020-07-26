<?php

declare(strict_types=1);

namespace Migrify\ZephirToPhp;

use Migrify\ZephirToPhp\Contract\CaseConverter\CaseConverterInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ZephirToPhpConverter
{
    /**
     * @var CaseConverterInterface[]
     */
    private $caseConverters = [];

    /**
     * @param CaseConverterInterface[] $caseConverters
     */
    public function __construct(array $caseConverters)
    {
        $this->caseConverters = $caseConverters;
    }

    public function convertFile(SmartFileInfo $fileInfo): string
    {
        $content = $fileInfo->getContents();

        foreach ($this->caseConverters as $caseConverter) {
            $content = $caseConverter->convertContent($content);
        }

        return $content;
    }
}
