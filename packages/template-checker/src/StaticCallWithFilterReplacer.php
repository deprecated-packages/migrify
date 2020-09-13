<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @see \Migrify\TemplateChecker\Tests\StaticCallWithFilterReplacer\StaticCallWithFilterReplacerTest
 */
final class StaticCallWithFilterReplacer
{
    /**
     * @var string
     * @see https://regex101.com/r/7lImz9/3
     * @see https://stackoverflow.com/a/35271017/1348344 for bracket matching on arguments
     */
    private const STATIC_CALL_PATTERN = '#\b(?<class>[A-Z][\w\\\\]+)::(?<method>[\w]+)\((?<arguments>(?:[^)(]+|\((?:[^)(]+|\([^)(]*\))*\))*)\)#m';

    public function processFileInfo(SplFileInfo $fileInfo): string
    {
        $contents = $fileInfo->getContents();

        return Strings::replace($contents, self::STATIC_CALL_PATTERN, static function (array $match) {
            if (in_array($match['class'], [Strings::class, DateTime::class], true)) {
                // no change
                return $match[0];
            }

            return $match['method'] . '(' . $match['arguments'] . ')';
        });
    }
}
