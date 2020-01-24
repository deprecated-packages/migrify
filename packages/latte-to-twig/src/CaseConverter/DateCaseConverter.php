<?php

declare(strict_types=1);

namespace Migrify\LatteToTwig\CaseConverter;

use Migrify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;
use Nette\Utils\Strings;

final class DateCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 420;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace($content, '#(({%|{{).*?) date\((.*?)\)#s', '$1 "now"|date($3)');
    }
}
