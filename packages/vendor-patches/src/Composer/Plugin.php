<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;

final class Plugin implements PluginInterface, Capable
{
    public function activate(Composer $composer, IOInterface $io): void
    {
        // Intentionally do nothing.
    }

    public function getCapabilities()
    {
        return [
            'Composer\Plugin\Capability\CommandProvider' => 'Migrify\VendorPatches\Composer\CommandProvider',
        ];
    }
}
