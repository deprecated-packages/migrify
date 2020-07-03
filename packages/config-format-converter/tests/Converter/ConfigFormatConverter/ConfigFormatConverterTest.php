<?php

declare(strict_types=1);

namespace Migrify\ConfigFormatConverter\Tests\Converter\ConfigFormatConverter;

use Migrify\ConfigFormatConverter\Converter\ConfigFormatConverter;
use Migrify\ConfigFormatConverter\HttpKernel\ConfigFormatConverterKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConfigFormatConverterTest extends AbstractKernelTestCase
{
    /**
     * @var ConfigFormatConverter
     */
    private $configFormatConverter;

    protected function setUp(): void
    {
        $this->bootKernel(ConfigFormatConverterKernel::class);

        $this->configFormatConverter = self::$container->get(ConfigFormatConverter::class);
    }

    public function test(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixture/some.xml');
        $convertedContent = $this->configFormatConverter->convert($fileInfo, 'yaml');

        $expectedFileInfo = new SmartFileInfo(__DIR__ . '/Source/expected.yaml');
        $this->assertSame($expectedFileInfo->getContents(), $convertedContent);
    }
}
