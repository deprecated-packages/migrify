<?php

declare(strict_types=1);

namespace Migrify\MigrifyKernel\Bundle;

use Migrify\MigrifyKernel\DependencyInjection\Extension\MigrifyKernelExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;

final class MigrifyKernelBundle extends Bundle
{
    public function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }

    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new MigrifyKernelExtension();
    }
}
