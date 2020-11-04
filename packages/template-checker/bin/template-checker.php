<?php

declare(strict_types=1);

use Migrify\MigrifyKernel\Bootstrap\KernelBootAndApplicationRun;
use Migrify\TemplateChecker\HttpKernel\TemplateCheckerKernel;

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

// autoload project
$projectAutoloadFile = getcwd() . '/vendor/autoload.php';
if (file_exists($projectAutoloadFile)) {
    require_once $projectAutoloadFile;
}

$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(TemplateCheckerKernel::class);
$kernelBootAndApplicationRun->run();
