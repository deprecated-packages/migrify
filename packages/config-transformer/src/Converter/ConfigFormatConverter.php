<?php

declare(strict_types=1);

namespace Migrify\ConfigTransformer\Converter;

use Migrify\ConfigTransformer\Collector\XmlImportCollector;
use Migrify\ConfigTransformer\ConfigLoader;
use Migrify\ConfigTransformer\DependencyInjection\ContainerBuilderCleaner;
use Migrify\ConfigTransformer\DumperFactory;
use Migrify\ConfigTransformer\DumperFomatter\YamlDumpFormatter;
use Migrify\ConfigTransformer\ValueObject\Format;
use Migrify\MigrifyKernel\Exception\NotImplementedYetException;
use Migrify\MigrifyKernel\Exception\ShouldNotHappenException;
use Migrify\PhpConfigPrinter\Provider\CurrentFilePathProvider;
use Migrify\PhpConfigPrinter\YamlToPhpConverter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;
use Symplify\SmartFileSystem\SmartFileInfo;

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

    /**
     * @var YamlToPhpConverter
     */
    private $yamlToPhpConverter;

    /**
     * @var CurrentFilePathProvider
     */
    private $currentFilePathProvider;

    /**
     * @var XmlImportCollector
     */
    private $xmlImportCollector;

    public function __construct(
        ConfigLoader $configLoader,
        DumperFactory $dumperFactory,
        ContainerBuilderCleaner $containerBuilderCleaner,
        YamlDumpFormatter $yamlDumpFormatter,
        YamlToPhpConverter $yamlToPhpConverter,
        CurrentFilePathProvider $currentFilePathProvider,
        XmlImportCollector $xmlImportCollector
    ) {
        $this->configLoader = $configLoader;
        $this->dumperFactory = $dumperFactory;
        $this->containerBuilderCleaner = $containerBuilderCleaner;
        $this->yamlDumpFormatter = $yamlDumpFormatter;
        $this->yamlToPhpConverter = $yamlToPhpConverter;
        $this->currentFilePathProvider = $currentFilePathProvider;
        $this->xmlImportCollector = $xmlImportCollector;
    }

    public function convert(SmartFileInfo $smartFileInfo, string $inputFormat, string $outputFormat): string
    {
        $this->currentFilePathProvider->setFilePath($smartFileInfo->getRealPath());

        $containerBuilderAndFileContent = $this->configLoader->createAndLoadContainerBuilderFromFileInfo(
            $smartFileInfo
        );

        $containerBuilder = $containerBuilderAndFileContent->getContainerBuilder();
        if ($outputFormat === Format::YAML) {
            $dumpedYaml = $this->dumpContainerBuilderToYaml($containerBuilder);
            return $this->decorateWithCollectedXmlImports($dumpedYaml);
        }

        if ($outputFormat === Format::PHP) {
            if ($inputFormat === Format::YAML) {
                $dumpedYaml = $containerBuilderAndFileContent->getFileContent();
                $dumpedYaml = $this->decorateWithCollectedXmlImports($dumpedYaml);

                return $this->yamlToPhpConverter->convert($dumpedYaml);
            }

            if ($inputFormat === Format::XML) {
                $dumpedYaml = $this->dumpContainerBuilderToYaml($containerBuilder);
                $dumpedYaml = $this->decorateWithCollectedXmlImports($dumpedYaml);

                return $this->yamlToPhpConverter->convert($dumpedYaml);
            }
        }

        $message = sprintf('Converting from %s to %s it not support yet', $inputFormat, $outputFormat);
        throw new NotImplementedYetException($message);
    }

    private function dumpContainerBuilderToYaml(ContainerBuilder $containerBuilder): string
    {
        $dumper = $this->dumperFactory->createFromContainerBuilderAndOutputFormat($containerBuilder, Format::YAML);
        $this->containerBuilderCleaner->cleanContainerBuilder($containerBuilder);

        $content = $dumper->dump();
        if (! is_string($content)) {
            throw new ShouldNotHappenException();
        }

        return $this->yamlDumpFormatter->format($content);
    }

    private function decorateWithCollectedXmlImports(string $dumpedYaml): string
    {
        $collectedXmlImports = $this->xmlImportCollector->provide();
        if ($collectedXmlImports === []) {
            return $dumpedYaml;
        }

        $yamlArray = Yaml::parse($dumpedYaml, Yaml::PARSE_CUSTOM_TAGS);
        $yamlArray['imports'] = array_merge($yamlArray['imports'] ?? [], $collectedXmlImports);

        return Yaml::dump($yamlArray, 10, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
    }
}
