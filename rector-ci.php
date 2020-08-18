<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Decomplex\Rector\MethodCall\UseMessageVariableForSprintfInSymfonyStyleRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Privatization\Rector\ClassMethod\PrivatizeLocalOnlyMethodRector;
use Rector\Set\ValueObject\SetList;
use Rector\SOLID\Rector\ClassMethod\UseInterfaceOverImplementationInConstructorRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::IMPORT_DOC_BLOCKS, true);

    $parameters->set(Option::SETS, [
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::PHP_70,
        SetList::PHP_71,
        SetList::PHP_72,
        SetList::PRIVATIZATION,
        SetList::SOLID,
        SetList::NAMING,
    ]);

    $parameters->set(Option::PATHS, [__DIR__ . '/packages']);

    $parameters->set(Option::EXCLUDE_PATHS, [
        __DIR__ . '/packages/vendor-patches/tests/Finder/*',
        __DIR__ . '/packages/**/Source/**',
        __DIR__ . '/packages/config-transformer/packages/format-switcher/tests/Converter/ConfigFormatConverter/YamlToPhp/Fixture/nested/*',
        __DIR__ . '/packages/diff-data-miner/src/Extractor/DefaultValueChangesExtractor.php',
    ]);

    $parameters->set(Option::EXCLUDE_RECTORS, [
        UseInterfaceOverImplementationInConstructorRector::class, PrivatizeLocalOnlyMethodRector::class
    ]);

    $services = $containerConfigurator->services();

    $services->set(UseMessageVariableForSprintfInSymfonyStyleRector::class);

    $services->set(StringClassNameToClassConstantRector::class)
        ->arg('$classesToSkip', ['Exception']);
};
