<?php

declare(strict_types=1);

namespace Migrify\LatteToTwig\CaseConverter;

use Migrify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;
use Nette\Utils\Strings;

final class CaptureCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 900;
    }

    public function convertContent(string $content): string
    {
        // {var $var = $anotherVar} =>
        // {% set var = anotherVar %}
        $content = Strings::replace($content, '#{var \$?(.*?) = \$?(.*?)}#s', '{% set $1 = $2 %}');

        // {capture $var}...{/capture} =>
        // {% set var %}...{% endset %}
        return Strings::replace($content, '#{capture \$(\w+)}(.*?){\/capture}#s', '{% set $1 %}$2{% endset %}');
    }
}
