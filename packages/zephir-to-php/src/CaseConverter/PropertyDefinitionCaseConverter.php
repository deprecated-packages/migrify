<?php

declare(strict_types=1);

namespace Migrify\ZephirToPhp\CaseConverter;

use Migrify\ZephirToPhp\Contract\CaseConverter\CaseConverterInterface;
use Nette\Utils\Strings;

final class PropertyDefinitionCaseConverter implements CaseConverterInterface
{
    public function convertContent(string $content): string
    {
        // public property;
        // ↓
        // public $property;
        $content = Strings::replace($content, '#(public|protected|private)\s+(\w+)\;$#m', '$1 $$2;');

        // let this->property
        // ↓
        // $this->property
        return Strings::replace($content, '#let\s+this#', '$this');
    }
}
