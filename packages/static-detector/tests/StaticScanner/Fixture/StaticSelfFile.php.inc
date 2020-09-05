<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\Tests\StaticScanner\Fixture;

final class StaticSelfFile
{
    public static function staticClassMethod(): void
    {
    }

    public function run()
    {
        self::staticClassMethod();
    }
}
