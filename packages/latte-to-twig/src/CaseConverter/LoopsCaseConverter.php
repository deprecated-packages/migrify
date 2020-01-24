<?php

declare(strict_types=1);

namespace Migrify\LatteToTwig\CaseConverter;

use Migrify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;
use Nette\Utils\Strings;

final class LoopsCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 400;
    }

    public function convertContent(string $content): string
    {
        // {foreach $values as $key => $value}...{/foreach} =>
        // {% for key, value in values %}...{% endfor %}
        $content = Strings::replace(
            $content,
            '#{foreach \$?(.*?) as \$([()\w ]+) => \$(\w+)}#i',
            '{% for $2, $3 in $1 %}'
        );

        // {foreach $values as [$value1, $value2]}...{/foreach} =>
        // {% for [value1, value2] in values %}...{% endfor %}
        $content = Strings::replace(
            $content,
            '#{foreach \$?(?<list>.*?) as (?<items>\[.*?\])}#i',
            function (array $match): string {
                return sprintf('{%% for %s in %s %%}', str_replace('$', '', $match['items']), $match['list']);
            }
        );

        // {foreach $values as $value}...{/foreach} =>
        // {% for value in values %}...{% endfor %}
        $content = Strings::replace($content, '#{foreach \$?(.*?) as \$([()\w ]+)}#i', '{% for $2 in $1 %}');
        $content = Strings::replace($content, '#{/foreach}#', '{% endfor %}');

        // {first}...{/first} =>
        // {% if loop.first %}...{% endif %}
        $content = Strings::replace($content, '#{first}(.*?){/first}#msi', '{% if loop.first %}$1{% endif %}');

        // {last}...{/last} =>
        // {% if loop.last %}...{% endif %}
        $content = Strings::replace($content, '#{last}(.*?){/last}#msi', '{% if loop.last %}$1{% endif %}');

        // {sep}, {/sep} => {% if loop.last == false %}, {% endif %}
        return Strings::replace($content, '#{sep}(.*?){\/sep}#msi', '{% if loop.last == false %}$1{% endif %}');
    }
}
