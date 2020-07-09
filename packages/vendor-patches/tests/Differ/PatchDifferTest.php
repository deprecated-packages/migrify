<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Tests\Differ;

use Migrify\VendorPatches\Differ\PatchDiffer;
use Migrify\VendorPatches\ValueObject\OldAndNewFileInfo;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PatchDifferTest extends AbstractKernelTestCase
{
    /**
     * @var PatchDiffer
     */
    private $patchDiffer;

    protected function setUp(): void
    {
        $this->patchDiffer = new PatchDiffer(
            new Differ(new UnifiedDiffOutputBuilder("--- Original\n+++ New\n", true))
        );
    }

    public function test(): void
    {
        $oldFileInfo = new SmartFileInfo(__DIR__ . '/PatchDifferSource/vendor/some/package/file.php.old');
        $newFileInfo = new SmartFileInfo(__DIR__ . '/PatchDifferSource/vendor/some/package/file.php');

        $oldAndNewFileInfo = new OldAndNewFileInfo($oldFileInfo, $newFileInfo, 'some/package');

        $diff = $this->patchDiffer->diff($oldAndNewFileInfo, $oldFileInfo->getRelativeFilePath());
        $this->assertStringEqualsFile(__DIR__ . '/PatchDifferFixture/expected_diff.php', $diff);
    }
}
