<?php

declare(strict_types=1);

namespace Migrify\Psr4Switcher\Tests\Utils;

use Iterator;
use Migrify\Psr4Switcher\HttpKernel\Psr4SwitcherKernel;
use Migrify\Psr4Switcher\Utils\MigrifyStrings;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class MigrifyStringsTest extends AbstractKernelTestCase
{
    /**
     * @var MigrifyStrings
     */
    private $migrifyStrings;

    protected function setUp(): void
    {
        $this->bootKernel(Psr4SwitcherKernel::class);
        $this->migrifyStrings = self::$container->get(MigrifyStrings::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(array $strings, string $expectedSharedSuffix): void
    {
        $sharedSuffix = $this->migrifyStrings->findSharedSlashedSuffix($strings);
        $this->assertSame($expectedSharedSuffix, $sharedSuffix);
    }

    public function provideData(): Iterator
    {
        yield [['Car', 'BusCar'], 'Car'];
        yield [['Apple\Pie', 'LikeAn\Apple\Pie'], 'Apple/Pie'];
        yield [['Apple/Pie', 'LikeAn\Apple\Pie'], 'Apple/Pie'];
        yield [['Components\ChatFriends', 'ChatFriends\ChatFriends'], 'ChatFriends'];
    }
}
