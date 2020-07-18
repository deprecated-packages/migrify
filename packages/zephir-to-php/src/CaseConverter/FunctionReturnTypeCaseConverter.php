<?php

declare(strict_types=1);

namespace Migrify\ZephirToPhp\CaseConverter;

use Migrify\ZephirToPhp\Contract\CaseConverter\CaseConverterInterface;
use Nette\Utils\Strings;

final class FunctionReturnTypeCaseConverter implements CaseConverterInterface
{
    /**
     * @see https://regex101.com/r/2hIuBu/1
     * @var string
     */
    private const REPLACE_RETURN_TYPE_PATTERN = '#(?<pre_content>function \w+\(.*?\))(\s+)?\-\>(\s+)?(?<type>.*?)$#m';

    public function convertContent(string $content): string
    {
        // return() -> int|string
        // ↓
        // return()

        return Strings::replace(
            $content,
            self::REPLACE_RETURN_TYPE_PATTERN,
            function (array $match) {
                // union type
                if (Strings::contains($match['type'], '|')) {
                    return $match['pre_content'];
                }

                // remove wrapper around objects
                $type = trim($match['type'], '<>');

                return $match['pre_content'] . ': ' . $type;
            }
        );
    }
}
