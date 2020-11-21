<?php

declare(strict_types=1);

namespace Migrify\ClassPresence\ValueObject;

final class StaticCheckedFileSuffix
{
    /**
     * @var string[]
     */
    public const SUFFIXES = ['yml', 'yaml', 'twig', 'latte', 'neon', 'php'];

    /**
     * @return string[]
     */
    public static function getSuffixesRegex(): array
    {
        $regexes = [];
        foreach (self::SUFFIXES as $suffix) {
            $regexes[] = '*.' . $suffix;
        }

        return $regexes;
    }
}
