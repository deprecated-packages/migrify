<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Differ;

use Migrify\VendorPatches\ValueObject\OldAndNewFileInfo;
use Nette\Utils\Strings;
use SebastianBergmann\Diff\Differ;

/**
 * @see \Migrify\VendorPatches\Tests\Differ\PatchDifferTest
 */
final class PatchDiffer
{
    /**
     * @var Differ
     */
    private $differ;

    public function __construct(Differ $differ)
    {
        $this->differ = $differ;
    }

    public function diff(OldAndNewFileInfo $oldAndNewFileInfo, string $directory): string
    {
        $oldFileInfo = $oldAndNewFileInfo->getOldFileInfo();
        $newFileInfo = $oldAndNewFileInfo->getNewFileInfo();

        $diff = $this->differ->diff($oldFileInfo->getContents(), $newFileInfo->getContents());

        $diff = Strings::replace($diff, '#^--- Original#', '--- /dev/null');
        return Strings::replace(
            $diff,
            '#^\+\+\+ New#m',
            '+++ ' . $newFileInfo->getRelativeFilePathFromDirectory($directory)
        );
    }
}
