<?php

declare(strict_types=1);

use Migrify\EasyCI\ValueObject\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    // default values
    $parameters->set(Option::SONAR_ORGANIZATION, null);
    $parameters->set(Option::SONAR_PROJECT_KEY, null);
    $parameters->set(Option::SONAR_DIRECTORIES, []);

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Migrify\EasyCI\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/HttpKernel', __DIR__ . '/../src/ValueObject']);
};
