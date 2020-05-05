<?php

declare(strict_types=1);

namespace Migrify\PhpNoc\PhpParser;

use PhpParser\Parser;
use PhpParser\ParserFactory;

final class PhpParserFactory
{
    public function create(): Parser
    {
        return (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
    }
}
