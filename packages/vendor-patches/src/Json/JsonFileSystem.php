<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Json;

use Nette\Utils\Json;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileSystem;

final class JsonFileSystem
{
    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(FileSystemGuard $fileSystemGuard, SmartFileSystem $smartFileSystem)
    {
        $this->fileSystemGuard = $fileSystemGuard;
        $this->smartFileSystem = $smartFileSystem;
    }

    public function loadFilePathToJson(string $filePath): array
    {
        $this->fileSystemGuard->ensureFileExists($filePath, __METHOD__);

        $fileContent = $this->smartFileSystem->readFile($filePath);
        return Json::decode($fileContent, Json::FORCE_ARRAY);
    }

    public function writeJsonToFilePath(array $jsonArray, string $filePath): void
    {
        $jsonContent = Json::encode($jsonArray, Json::PRETTY) . PHP_EOL;
        $this->smartFileSystem->dumpFile($filePath, $jsonContent);
    }
}
