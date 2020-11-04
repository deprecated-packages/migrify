<?php

declare(strict_types=1);

use Migrify\MigrifyKernel\Bootstrap\KernelBootAndApplicationRun;
use Migrify\SnifferFixerToECS\HttpKernel\SnifferFixerToECSKernel;

$possibleAutoloadPaths = [
    // after split package
    __DIR__ . '/../vendor/autoload.php',
    // dependency
    __DIR__ . '/../../../autoload.php',
    // monorepo
    __DIR__ . '/../../../vendor/autoload.php',
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (file_exists($possibleAutoloadPath)) {
        require_once $possibleAutoloadPath;

        break;
    }
}

$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(SnifferFixerToECSKernel::class);
$kernelBootAndApplicationRun->run();
