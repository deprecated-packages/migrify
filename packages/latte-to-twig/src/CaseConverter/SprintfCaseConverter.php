<?php

declare(strict_types=1);

namespace Migrify\LatteToTwig\CaseConverter;

use Migrify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;
use Nette\Utils\Strings;

final class SprintfCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 450;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace($content, '#{%(.*?)sprintf\((.*?), ?(.*?)\)#s', '{%$1$2|format($3)');
    }
}
