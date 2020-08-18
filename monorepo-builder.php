<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\AddTagToChangelogReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::DATA_TO_REMOVE, [
        'require' => ['tracy/tracy' => '*', 'phpunit/phpunit' => '*']
    ]);

    // @todo add asteristk support for packages split to symplify
    $parameters->set(Option::DIRECTORIES_TO_REPOSITORIES, [
        'packages/class-presence' => 'git@github.com:migrify/class-presence.git',
        'packages/diff-data-miner' => 'git@github.com:migrify/diff-data-miner.git',
        'packages/config-transformer' => 'git@github.com:migrify/config-transformer.git',
        'packages/easy-ci' => 'git@github.com:migrify/easy-ci.git',
        'packages/fatal-error-scanner' => 'git@github.com:migrify/fatal-error-scanner.git',
        'packages/latte-to-twig' => 'git@github.com:migrify/latte-to-twig.git',
        'packages/neon-to-yaml' => 'git@github.com:migrify/neon-to-yaml.git',
        'packages/psr4-switcher' => 'git@github.com:migrify/psr4-switcher.git',
        'packages/symfony-route-usage' => 'git@github.com:migrify/symfony-route-usage.git',
        'packages/vendor-patches' => 'git@github.com:migrify/vendor-patches.git',
        'packages/php-config-printer' => 'git@github.com:migrify/php-config-printer.git'
    ]);

    $services = $containerConfigurator->services();

    $services->set(SetCurrentMutualDependenciesReleaseWorker::class);
    $services->set(AddTagToChangelogReleaseWorker::class);
    $services->set(TagVersionReleaseWorker::class);
    $services->set(PushTagReleaseWorker::class);
    $services->set(SetNextMutualDependenciesReleaseWorker::class);
    $services->set(UpdateBranchAliasReleaseWorker::class);
    $services->set(PushNextDevReleaseWorker::class);
};
