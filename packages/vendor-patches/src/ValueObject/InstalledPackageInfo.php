<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\ValueObject;

final class InstalledPackageInfo
{
    /**
     * @var string
     */
    private $packageName;

    /**
     * @var string
     */
    private $installationDirectory;

    public function __construct(string $packageName, string $installationDirectory)
    {
        $this->packageName = $packageName;
        $this->installationDirectory = $installationDirectory;
    }

    public function getPackageName(): string
    {
        return $this->packageName;
    }

    public function getInstallationDirectory(): string
    {
        return $this->installationDirectory;
    }
}
