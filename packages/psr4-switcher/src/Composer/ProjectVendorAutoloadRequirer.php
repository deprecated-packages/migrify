<?php

declare(strict_types=1);

namespace Migrify\Psr4Switcher\Composer;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\SmartFileSystem\FileSystemGuard;

final class ProjectVendorAutoloadRequirer
{
    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(FileSystemGuard $fileSystemGuard, SymfonyStyle $symfonyStyle)
    {
        $this->fileSystemGuard = $fileSystemGuard;
        $this->symfonyStyle = $symfonyStyle;
    }

    public function loadProjectVendorAutoload(ComposerJson $composerJson, string $composerJsonPath): void
    {
        $projectAutoloadFile = $this->resolveProjectAutoloadFile($composerJson, $composerJsonPath);
        $this->fileSystemGuard->ensureFileExists($projectAutoloadFile, __METHOD__);

        $projectAutoloadFile = realpath($projectAutoloadFile);
        require_once $projectAutoloadFile;
        $message = sprintf('File "%s" was required', $projectAutoloadFile);
        $this->symfonyStyle->note($message);
    }

    private function resolveProjectAutoloadFile(ComposerJson $composerJson, string $composerJsonPath): string
    {
        // A. default vendor location
        $projectDirectory = dirname($composerJsonPath);
        $projectAutoloadFile = $projectDirectory . '/vendor/autoload.php';
        if (file_exists($projectAutoloadFile)) {
            return $projectAutoloadFile;
        }

        // B. custom vendor location
        $customVendorDirectory = $composerJson->getConfig()['vendor-dir'] ?? null;
        return $projectDirectory . '/' . $customVendorDirectory . '/autoload.php';
    }
}
