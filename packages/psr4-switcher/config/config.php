<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Migrify\Psr4Switcher\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/HttpKernel/*', __DIR__ . '/../src/ValueObject/*']);

    $services->set(SymfonyStyleFactory::class);

    $services->set(SymfonyStyle::class)
        ->factory([ref(SymfonyStyleFactory::class), 'create']);

    $services->set(FinderSanitizer::class);

    $services->set(FileSystemGuard::class);
};
