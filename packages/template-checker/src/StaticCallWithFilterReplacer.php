<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker;

use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @see \Migrify\TemplateChecker\Tests\StaticCallWithFilterReplacer\StaticCallWithFilterReplacerTest
 */
final class StaticCallWithFilterReplacer
{
    /**
     * @var string
     * @see https://regex101.com/r/mDzFKI/2
     */
    private const STATIC_CALL_PATTERN = '#\b(?<class>[A-Z][\w\\\\]+)::(?<method>[\w]+)\((.*?)?\)#m';

    public function processFileInfo(SplFileInfo $fileInfo): string
    {
        $contents = $fileInfo->getContents();

        return Strings::replace($contents, self::STATIC_CALL_PATTERN, static function (array $match) {
            return '(' . $match['3'] . '|' . $match['method'] . ')';
        });
    }
}
