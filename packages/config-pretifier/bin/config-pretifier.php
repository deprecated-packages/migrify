<?php

declare(strict_types=1);

use Migrify\ConfigPretifier\HttpKernel\ConfigPretifierKernel;
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

// autoload local project path, if not installed ad vendor dependency
$projectVendorAutoload = getcwd() . '/vendor/autoload';
if (file_exists($projectVendorAutoload)) {
    require_once $projectVendorAutoload;
}

$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(ConfigPretifierKernel::class);
$kernelBootAndApplicationRun->run();
