<?php

declare(strict_types=1);

namespace Migrify\ConfigPretifier\Tests\Pretifier\NeonConfigPretifier;

use Iterator;
use Migrify\ConfigPretifier\HttpKernel\ConfigPretifierKernel;
use Migrify\ConfigPretifier\Pretifier\NeonConfigPretifier;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NeonConfigPretifierTest extends AbstractKernelTestCase
{
    /**
     * @var NeonConfigPretifier
     */
    private $neonConfigPretifier;

    protected function setUp(): void
    {
        $this->bootKernel(ConfigPretifierKernel::class);
        $this->neonConfigPretifier = self::$container->get(NeonConfigPretifier::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToInputAndExpected($fileInfo);

        $changedContent = $this->neonConfigPretifier->pretify($inputAndExpected->getInput());
        $this->assertSame($inputAndExpected->getExpected() . PHP_EOL, $changedContent);
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.neon');
    }
}
