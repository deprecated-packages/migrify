namespace Migrify\ZephirToPhp\Tests\ZephirToPhpConverter\Fixture;

class LetProperty
{
    protected property;
    public function run()
    {
        let this->property = 100;
    }
}
-----
<?php

namespace Migrify\ZephirToPhp\Tests\ZephirToPhpConverter\Fixture;

class LetProperty
{
    protected $property;
    public function run()
    {
        $this->property = 100;
    }
}
