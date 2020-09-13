<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker\Tests\LattePersistence;

use Iterator;
use Latte\Engine;
use Migrify\TemplateChecker\Tests\LattePersistence\Source\PlusFilterProvider;
use PHPUnit\Framework\TestCase;

/**
 * This is a meta test for @see \Migrify\TemplateChecker\StaticCallWithFilterReplacer
 * To verify that the filter behaves the same as static function
 */
final class LatteFilterPersistenceTest extends TestCase
{
    /**
     * @var Engine
     */
    private $latteEngine;

    protected function setUp(): void
    {
        $this->latteEngine = new Engine();

        $plusFilterProvider = new PlusFilterProvider();
        $this->latteEngine->addFilter($plusFilterProvider->getName(), $plusFilterProvider);
    }

    /**
     * Fixture testing is based on @see https://github.com/symplify/easy-testing
     * @dataProvider provideData()
     */
    public function testFilter(
        string $inputFilterFilePath,
        string $inputStaticCallFilePath,
        string $expectedContent
    ): void {
        $result = $this->latteEngine->renderToString($inputFilterFilePath);
        $contentWithoutSpaces = trim($result);
        $this->assertSame($expectedContent, $contentWithoutSpaces);

        $result = $this->latteEngine->renderToString($inputStaticCallFilePath);
        $contentWithoutSpaces = trim($result);
        $this->assertSame($expectedContent, $contentWithoutSpaces);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/latte_filter.latte', __DIR__ . '/Fixture/latte_static_call.latte', '7'];

        yield [__DIR__ . '/Fixture/latte_filter_with_bracket.latte', __DIR__ . '/Fixture/latte_static_call.latte', '7'];
    }
}
