namespace Migrify\ZephirToPhp\Tests\ZephirToPhpConverter\Fixture;

class ClassMethods
{
    public function doSum1(int a, int b) -> int
    {
        return a + b;
    }

    public function tooEmpty() -> void
    {
    }

    public function getSomeObject() -> <SomeObject>
    {
    }
}
-----
<?php

namespace Migrify\ZephirToPhp\Tests\ZephirToPhpConverter\Fixture;

class ClassMethods
{
    public function doSum1(int $a, int $b): int
    {
        return a + b;
    }

    public function tooEmpty(): void
    {
    }

    public function getSomeObject(): SomeObject
    {
    }
}
