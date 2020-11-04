<?php

declare(strict_types=1);

use Migrify\DiffDataMiner\HttpKernel\DiffDataMinerKernel;
use Migrify\MigrifyKernel\Bootstrap\KernelBootAndApplicationRun;

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

$diffDataMinerKernel = new KernelBootAndApplicationRun(DiffDataMinerKernel::class);
$diffDataMinerKernel->run();
