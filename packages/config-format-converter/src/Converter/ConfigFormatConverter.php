<?php

declare(strict_types=1);

namespace Migrify\ConfigFormatConverter\Converter;

use Migrify\ConfigFormatConverter\ConfigLoader;
use Migrify\ConfigFormatConverter\DependencyInjection\ContainerBuilderCleaner;
use Migrify\ConfigFormatConverter\DumperFactory;
use Migrify\ConfigFormatConverter\DumperFomatter\YamlDumpFormatter;
use Migrify\ConfigFormatConverter\Exception\ShouldNotHappenException;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Migrify\ConfigFormatConverter\Tests\Converter\ConfigFormatConverter\ConfigFormatConverterTest
 */
final class ConfigFormatConverter
{
    /**
     * @var ConfigLoader
     */
    private $configLoader;

    /**
     * @var DumperFactory
     */
    private $dumperFactory;

    /**
     * @var ContainerBuilderCleaner
     */
    private $containerBuilderCleaner;

    /**
     * @var YamlDumpFormatter
     */
    private $yamlDumpFormatter;

    public function __construct(
        ConfigLoader $configLoader,
        DumperFactory $dumperFactory,
        ContainerBuilderCleaner $containerBuilderCleaner,
        YamlDumpFormatter $yamlDumpFormatter
    ) {
        $this->configLoader = $configLoader;
        $this->dumperFactory = $dumperFactory;
        $this->containerBuilderCleaner = $containerBuilderCleaner;
        $this->yamlDumpFormatter = $yamlDumpFormatter;
    }

    public function convert(SmartFileInfo $smartFileInfo, string $outputFormat): string
    {
        $containerBuilder = $this->configLoader->loadContainerBuilderFromFileInfo($smartFileInfo);
        $this->containerBuilderCleaner->cleanContainerBuilder($containerBuilder);

        $dumper = $this->dumperFactory->createFromContainerBuilderAndOutputFormat(
            $containerBuilder,
            $outputFormat
        );

        $content = $dumper->dump();
        if (! is_string($content)) {
            throw new ShouldNotHappenException();
        }

        return $this->yamlDumpFormatter->format($content);
    }
}
