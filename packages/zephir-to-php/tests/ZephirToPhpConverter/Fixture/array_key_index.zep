namespace Migrify\ZephirToPhp\Tests\ZephirToPhpConverter\Fixture;

class ArrayKeyIndex
{
    public function assoc()
    {
        return ["test": true, "first": "ok", 3: 123];
    }
}
-----
<?php

namespace Migrify\ZephirToPhp\Tests\ZephirToPhpConverter\Fixture;

class ArrayKeyIndex
{
    public function assoc()
    {
        return ["test" => true, "first" => "ok", 3 => 123];
    }
}
