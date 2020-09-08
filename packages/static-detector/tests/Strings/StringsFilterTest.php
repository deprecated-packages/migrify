<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\Tests\Strings;

use Iterator;
use Migrify\StaticDetector\HttpKernel\StaticDetectorKernel;
use Migrify\StaticDetector\Strings\StringsFilter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class StringsFilterTest extends AbstractKernelTestCase
{
    /**
     * @var StringsFilter
     */
    private $stringsFilter;

    protected function setUp(): void
    {
        self::bootKernel(StaticDetectorKernel::class);
        $this->stringsFilter = self::$container->get(StringsFilter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $inputValue, array $matchingValues, bool $expectedIsMatch): void
    {
        $isMatch = $this->stringsFilter->isMatchOrFnMatch($inputValue, $matchingValues);
        $this->assertSame($expectedIsMatch, $isMatch);
    }

    public function provideData(): Iterator
    {
        yield ['some', [], true];
        yield ['some', ['another'], false];
        yield ['Etra', ['tra'], false];
        // fnmatch
        yield ['Etra', ['*tra'], true];
        yield ['Etra', ['Etr*'], true];
        yield ['Etra\\Large', ['Etr*'], true];
        yield ['Etra\\Large', ['Etr*'], true];
        yield ['Etra\\Large', ['\\Etr*'], false];
    }
}
