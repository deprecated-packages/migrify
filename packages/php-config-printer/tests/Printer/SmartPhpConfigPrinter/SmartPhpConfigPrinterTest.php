<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter;

use Iterator;
use Migrify\PhpConfigPrinter\HttpKernel\PhpConfigPrinterKernel;
use Migrify\PhpConfigPrinter\Printer\SmartPhpConfigPrinter;
use Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\ClassWithConstants;
use Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\FirstClass;
use Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\SecondClass;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class SmartPhpConfigPrinterTest extends AbstractKernelTestCase
{
    /**
     * @var SmartPhpConfigPrinter
     */
    private $smartPhpConfigPrinter;

    protected function setUp(): void
    {
        $this->bootKernel(PhpConfigPrinterKernel::class);
        $this->smartPhpConfigPrinter = self::$container->get(SmartPhpConfigPrinter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(array $services, string $expectedContentFilePath): void
    {
        $printedContent = $this->smartPhpConfigPrinter->printConfiguredServices($services);
        $this->assertStringEqualsFile($expectedContentFilePath, $printedContent);
    }

    public function provideData(): Iterator
    {
        yield [[
            FirstClass::class => [
                'some_key' => 'some_value',
            ],
            SecondClass::class => null,
        ], __DIR__ . '/Fixture/expected_file.php.inc'];

        yield [[
            ClassWithConstants::class => [
                ClassWithConstants::CONFIG_KEY => 'it is constant',
                ClassWithConstants::NUMERIC_CONFIG_KEY => 'a lot of numbers',
            ],
        ], __DIR__ . '/Fixture/expected_constant_file.php.inc'];
    }
}
