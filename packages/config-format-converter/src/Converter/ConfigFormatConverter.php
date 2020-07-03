<?php

declare(strict_types=1);

namespace Migrify\ConfigFormatConverter\Converter;

use Migrify\ConfigFormatConverter\ConfigLoader;
use Migrify\ConfigFormatConverter\DumperFactory;
use Migrify\ConfigFormatConverter\Exception\ShouldNotHappenException;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
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

    public function __construct(ConfigLoader $configLoader, DumperFactory $dumperFactory)
    {
        $this->configLoader = $configLoader;
        $this->dumperFactory = $dumperFactory;
    }

    public function convert(SmartFileInfo $smartFileInfo, string $outputFormat): string
    {
        $containerBuilder = $this->configLoader->loadContainerBuilderFromFileInfo($smartFileInfo);

        $this->removeSymfonyInternalServices($containerBuilder);

        $dumper = $this->dumperFactory->createFromContainerBuilderAndOutputFormat($containerBuilder, $outputFormat);

        $content = $dumper->dump();
        if (! is_string($content)) {
            throw new ShouldNotHappenException();
        }

        return ltrim($content);
    }

    private function removeSymfonyInternalServices(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->removeDefinition('service_container');
        $containerBuilder->removeAlias(PsrContainerInterface::class);
        $containerBuilder->removeAlias(ContainerInterface::class);
    }
}
