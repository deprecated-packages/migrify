namespace Migrify\ZephirToPhp\Tests\ZephirToPhpConverter\Fixture;

class RemoveUnionType
{
    public function unionTypes() -> int|string
    {
    }
}
-----
<?php

namespace Migrify\ZephirToPhp\Tests\ZephirToPhpConverter\Fixture;

class RemoveUnionType
{
    public function unionTypes()
    {
    }
}
