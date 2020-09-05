<?php

declare(strict_types=1);

namespace Migrify\MigrifyKernel\Bootstrap;

use Migrify\MigrifyKernel\Exception\BootException;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\KernelInterface;
use Symplify\PackageBuilder\Console\Input\InputDetector;

final class KernelBootAndApplicationRun
{
    /**
     * @var class-string
     */
    private $kernelClass;

    /**
     * @param class-string $kernelClass
     */
    public function __construct(string $kernelClass)
    {
        $this->kernelClass = $kernelClass;
    }

    public function run(): void
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();

        /** @var Application|null $application */
        $application = $container->get(Application::class);
        if ($application === null) {
            $message = sprintf(
                'Application class %s was not found. Make it public in the config.',
                Application::class
            );
            throw new BootException($message);
        }

        exit($application->run());
    }

    private function createKernel(): KernelInterface
    {
        // random has is needed, so cache is invalidated and changes from config are loaded
        $environment = 'prod' . random_int(1, 100000);
        $kernelClass = $this->kernelClass;

        return new $kernelClass($environment, InputDetector::isDebug());
    }
}
