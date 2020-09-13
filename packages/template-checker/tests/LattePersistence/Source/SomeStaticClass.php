<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker\Tests\LattePersistence\Source;

final class SomeStaticClass
{
    public static function plus(int $number, int $anotherNumber): int
    {
        return $number + $anotherNumber;
    }
}
