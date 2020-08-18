<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\ChangelogLinker\Configuration\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('authors_to_ignore', ['TomasVotruba']);

    $parameters->set('package_aliases', ['SRU' => 'Symfony Route Usage']);
};
