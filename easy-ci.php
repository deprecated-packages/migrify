<?php

declare(strict_types=1);

use Migrify\EasyCI\ValueObject\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SONAR_ORGANIZATION, 'migrify');
    $parameters->set(Option::SONAR_PROJECT_KEY, 'migrify_migrify');
    $parameters->set(Option::SONAR_DIRECTORIES, [
        __DIR__ . '/packages'
    ]);
};
