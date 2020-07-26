<?php

declare(strict_types=1);

namespace Migrify\ZephirToPhp\Contract\CaseConverter;

interface CaseConverterInterface
{
    public function convertContent(string $content): string;
}
