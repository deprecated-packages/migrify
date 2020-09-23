<?php

declare(strict_types=1);

use Migrify\PhpConfigPrinter\ValueObject\Option;
use PhpParser\BuilderFactory;
use PhpParser\NodeFinder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Yaml\Parser;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::INLINE_VALUE_OBJECT_FUNC_CALL_NAME, 'Migrify\SymfonyPhpConfig\inline_value_object');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Migrify\PhpConfigPrinter\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/HttpKernel', __DIR__ . '/../src/Dummy']);

    $services->set(NodeFinder::class);
    $services->set(Parser::class);
    $services->set(BuilderFactory::class);

    $services->set(ParameterProvider::class);
};
