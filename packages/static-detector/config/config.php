<?php

declare(strict_types=1);

use Migrify\StaticDetector\NodeTraverser\StaticCollectNodeTraverser;
use Migrify\StaticDetector\NodeTraverser\StaticCollectNodeTraverserFactory;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileSystem;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire();

    $services->load('Migrify\StaticDetector\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/Exception',
            __DIR__ . '/../src/ValueObject',
            __DIR__ . '/../src/HttpKernel/StaticDetectorKernel.php',
        ]);

    $services->set(StaticCollectNodeTraverser::class)
        ->factory([ref(StaticCollectNodeTraverserFactory::class), 'create']);

    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)
        ->factory([ref(SymfonyStyleFactory::class), 'create']);

    $services->set(FinderSanitizer::class);
    $services->set(SmartFileSystem::class);
    $services->set(Finder::class);

    $services->set(ParserFactory::class);
    $services->set(Parser::class)
        ->factory([ref(ParserFactory::class), 'create'])
        ->arg('$kind', ParserFactory::PREFER_PHP7);
};
