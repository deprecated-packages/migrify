<?php

declare(strict_types=1);

namespace Migrify\LatteToTwig\CaseConverter;

use Migrify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;
use Nette\Utils\Strings;

final class CommentCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 800;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace($content, '#{\*(.*?)\*}#s', '{#$1#}');
    }
}
