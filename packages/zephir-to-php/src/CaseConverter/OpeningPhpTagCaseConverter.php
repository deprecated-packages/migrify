<?php

declare(strict_types=1);

namespace Migrify\ZephirToPhp\CaseConverter;

use Migrify\ZephirToPhp\Contract\CaseConverter\CaseConverterInterface;

final class OpeningPhpTagCaseConverter implements CaseConverterInterface
{
    public function convertContent(string $content): string
    {
        return '<?php' . PHP_EOL . PHP_EOL . $content;
    }
}
