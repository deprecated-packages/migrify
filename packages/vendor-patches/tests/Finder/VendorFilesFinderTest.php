<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Tests\Finder;

use Migrify\VendorPatches\Finder\OldToNewFilesFinder;
use Migrify\VendorPatches\ValueObject\InstalledPackageInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;

final class VendorFilesFinderTest extends AbstractKernelTestCase
{
    /**
     * @var OldToNewFilesFinder
     */
    private $vendorFilesFinder;

    protected function setUp(): void
    {
        $this->vendorFilesFinder = new OldToNewFilesFinder(new FinderSanitizer());
    }

    public function test(): void
    {
        $files = $this->vendorFilesFinder->find(new InstalledPackageInfo(
            'vendor/packagw',
            __DIR__ . '/VendorFilesFinderSource'
        ));

        $this->assertCount(1, $files);
    }
}
