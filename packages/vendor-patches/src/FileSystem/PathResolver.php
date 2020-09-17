<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\FileSystem;

use Migrify\MigrifyKernel\Exception\ShouldNotHappenException;
use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PathResolver
{
    /**
     * @see https://regex101.com/r/KhzCSu/1
     * @var string
     */
    private const VENDOR_PACKAGE_DIRECTORY_REGEX = '#^(?<vendor_package_directory>.*?vendor\/(\w|\.|\-)+\/(\w|\.|\-)+)\/#si';

    public function resolveVendorDirectory(SmartFileInfo $fileInfo): string
    {
        $match = Strings::match($fileInfo->getRealPath(), self::VENDOR_PACKAGE_DIRECTORY_REGEX);
        if (! isset($match['vendor_package_directory'])) {
            throw new ShouldNotHappenException('Could not resolve vendor package directory');
        }

        return $match['vendor_package_directory'];
    }
}
