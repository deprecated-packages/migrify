<?php

declare(strict_types=1);

namespace Migrify\LatteToTwig\CaseConverter;

use Migrify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;
use Nette\Utils\Strings;

final class SuffixCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 150;
    }

    public function convertContent(string $content): string
    {
        // suffix: "_snippets/menu.latte" => "_snippets/menu.twig"
        return Strings::replace($content, '#([\w/"]+).latte#', '$1.twig');
    }
}
