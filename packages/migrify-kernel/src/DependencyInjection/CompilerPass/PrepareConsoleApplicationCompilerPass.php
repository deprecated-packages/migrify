<?php

declare(strict_types=1);

namespace Migrify\MigrifyKernel\DependencyInjection\CompilerPass;

use Migrify\MigrifyKernel\Console\AutowiredConsoleApplication;
use Migrify\MigrifyKernel\Console\ConsoleApplicationFactory;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @todo replace with symplify/symplify-kernel after release of Symplify 9
 */
final class PrepareConsoleApplicationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $consoleApplicationClass = $this->resolveConsoleApplicationClass($containerBuilder);
        if ($consoleApplicationClass === null) {
            $this->registerAutowiredSymfonyConsole($containerBuilder);
            return;
        }

        // add console application alias
        if ($consoleApplicationClass === Application::class) {
            return;
        }

        $containerBuilder->setAlias(Application::class, $consoleApplicationClass)
            ->setPublic(true);
    }

    private function resolveConsoleApplicationClass(ContainerBuilder $containerBuilder): ?string
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            if (! is_a((string) $definition->getClass(), Application::class, true)) {
                continue;
            }

            return $definition->getClass();
        }

        return null;
    }

    /**
     * Missing console application? add basic one
     */
    private function registerAutowiredSymfonyConsole(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->autowire(AutowiredConsoleApplication::class, AutowiredConsoleApplication::class)
            ->setFactory([new Reference(ConsoleApplicationFactory::class), 'create']);

        $containerBuilder->setAlias(Application::class, AutowiredConsoleApplication::class)
            ->setPublic(true);
    }
}
