<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Finder;

use Migrify\VendorPatches\ValueObject\InstalledPackageInfo;
use Migrify\VendorPatches\ValueObject\OldAndNewFileInfo;
use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class OldToNewFilesFinder
{
    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    public function __construct(FinderSanitizer $finderSanitizer)
    {
        $this->finderSanitizer = $finderSanitizer;
    }

    /**
     * @return OldAndNewFileInfo[]
     */
    public function find(InstalledPackageInfo $packageInfo): array
    {
        $oldAndNewFileInfos = [];
        $oldFileInfos = $this->findSmartFileInfosInDirectory($packageInfo->getInstallationDirectory());

        foreach ($oldFileInfos as $oldFileInfo) {
            $newFilePath = rtrim($oldFileInfo->getRealPath(), '.old');
            if (! file_exists($newFilePath)) {
                continue;
            }

            $newFileInfo = new SmartFileInfo($newFilePath);

            $oldAndNewFileInfos[] = new OldAndNewFileInfo($oldFileInfo, $newFileInfo, $packageInfo->getPackageName());
        }

        return $oldAndNewFileInfos;
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findSmartFileInfosInDirectory(string $directory): array
    {
        $finder = Finder::create()
            ->in($directory)
            ->files()
            // excluded built files
            ->exclude('composer/')
            ->exclude('ocramius/')
            ->name('*.php.old');

        return $this->finderSanitizer->sanitize($finder);
    }
}
