<?php

declare(strict_types=1);

namespace Migrify\LatteToTwig;

use Migrify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;
use Migrify\LatteToTwig\Exception\ConfigurationException;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LatteToTwigConverter
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
        foreach ($caseConverters as $caseConverter) {
            $this->ensureCaseConverterPriorityIsUnique($caseConverter);
            $this->caseConverters[$caseConverter->getPriority()] = $caseConverter;
        }

        krsort($this->caseConverters);
    }

    public function convertFile(SmartFileInfo $fileInfo): string
    {
        $content = $fileInfo->getContents();

        foreach ($this->caseConverters as $caseConverter) {
            $content = $caseConverter->convertContent($content);
        }

        return $content;
    }

    private function ensureCaseConverterPriorityIsUnique(CaseConverterInterface $caseConverter): void
    {
        if (! isset($this->caseConverters[$caseConverter->getPriority()])) {
            return;
        }

        throw new ConfigurationException(sprintf(
            'Duplicate case converter priority: %s and %s',
            get_class($caseConverter),
            get_class($this->caseConverters[$caseConverter->getPriority()])
        ));
    }
}
