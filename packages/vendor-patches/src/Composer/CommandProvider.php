<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Composer;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Migrify\VendorPatches\Differ\PatchDiffer;
use Migrify\VendorPatches\Finder\OldToNewFilesFinder;
use Migrify\VendorPatches\Json\JsonFileSystem;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;

final class CommandProvider implements CommandProviderCapability
{
    public function getCommands()
    {
        return [new GenerateCommand(
            new OldToNewFilesFinder(new FinderSanitizer()),
            new PatchDiffer(new Differ(new UnifiedDiffOutputBuilder("--- Original\n+++ New\n", true))),
            new ComposerPatchesConfigurationUpdater(new JsonFileSystem(new FileSystemGuard()))
        )];
    }
}
