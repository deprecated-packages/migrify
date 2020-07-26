<?php

declare(strict_types=1);

namespace Migrify\ZephirToPhp\CaseConverter;

use Migrify\ZephirToPhp\Contract\CaseConverter\CaseConverterInterface;
use Nette\Utils\Strings;

final class ArrayKeyValueCaseConverter implements CaseConverterInterface
{
    public function convertContent(string $content): string
    {
        // [key: value]
        // ↓
        // [key => value]

        return Strings::replace($content, '#\[(?<array_content>.*?)\]#', function (array $match) {
            $phpArrayContent = Strings::replace($match['array_content'], '#:#', ' =>');

            return '[' . $phpArrayContent . ']';
        });
    }
}
