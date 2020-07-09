<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Composer;

use Composer\Command\BaseCommand;
use Composer\Factory;
use Composer\Package\PackageInterface;
use Migrify\VendorPatches\Differ\PatchDiffer;
use Migrify\VendorPatches\Finder\OldToNewFilesFinder;
use Migrify\VendorPatches\ValueObject\InstalledPackageInfo;
use Migrify\VendorPatches\ValueObject\OldAndNewFileInfo;
use Migrify\VendorPatches\ValueObject\PatchFileInfo;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class GenerateCommand extends BaseCommand
{
    /**
     * @var OldToNewFilesFinder
     */
    private $oldToNewFilesFinder;

    /**
     * @var PatchDiffer
     */
    private $patchDiffer;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ComposerPatchesConfigurationUpdater
     */
    private $composerPatchesConfigurationUpdater;

    public function __construct(
        OldToNewFilesFinder $vendorFilesFinder,
        PatchDiffer $patchDiffer,
        ComposerPatchesConfigurationUpdater $composerPatchesConfigurationUpdater
    ) {
        $this->oldToNewFilesFinder = $vendorFilesFinder;
        $this->patchDiffer = $patchDiffer;
        $this->composerPatchesConfigurationUpdater = $composerPatchesConfigurationUpdater;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('vendor-patches-generate');
        $this->setDescription('Generate patches for dependencies');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->symfonyStyle = new SymfonyStyle($input, $output);

        $composer = $this->getComposer();
        $packages = $composer->getRepositoryManager()->getLocalRepository()->getPackages();

        $packageInfos = array_map(function (PackageInterface $package) use ($composer) {
            return new InstalledPackageInfo(
                $package->getName(),
                $composer->getInstallationManager()->getInstallPath($package)
            );
        }, $packages);

        $packageInfos = array_filter($packageInfos, function (InstalledPackageInfo $packageInfo) {
            return $packageInfo->getInstallationDirectory() && strpos(
                $packageInfo->getInstallationDirectory(),
                'vendor-patches'
            ) === false;
        });

        $patchCollections = array_map(function (InstalledPackageInfo $packageInfo) {
            $this->symfonyStyle->note("Checking {$packageInfo->getInstallationDirectory()}");
            return $this->detectPatches($packageInfo);
        }, $packageInfos);
        $patches = array_merge(...$patchCollections);

        if ($patches !== []) {
            $message = sprintf('Great! %d new patch files added', count($patches));
            $this->symfonyStyle->success($message);
        } else {
            $this->symfonyStyle->success('No new patches were added');
        }

        return ShellCode::SUCCESS;
    }

    /**
     * @return PatchFileInfo[]
     */
    protected function detectPatches(InstalledPackageInfo $packageInfo): array
    {
        $composer = new SmartFileInfo(Factory::getComposerFile());

        $oldAndNewFileInfos = $this->oldToNewFilesFinder->find($packageInfo);

        $addedPatchFiles = [];
        $composerExtraPatches = [];

        foreach ($oldAndNewFileInfos as $oldAndNewFileInfo) {
            if ($oldAndNewFileInfo->isContentIdentical()) {
                $this->reportIdenticalNewAndOldFile($oldAndNewFileInfo);
                continue;
            }

            // write into patches file
            $patchFileRelativePath = $this->createPatchFilePath($oldAndNewFileInfo, $packageInfo);
            $composerExtraPatches[$oldAndNewFileInfo->getPackageName()][] = $patchFileRelativePath;

            $patchFileAbsolutePath = dirname($composer->getRealPath()) . DIRECTORY_SEPARATOR . $patchFileRelativePath;

            // dump the patch
            $patchDiff = $this->patchDiffer->diff($oldAndNewFileInfo, $packageInfo->getInstallationDirectory());
            $patch = new PatchFileInfo($patchDiff, $patchFileAbsolutePath);

            if ($this->doesPatchAlreadyExist($patch)) {
                $message = sprintf('Patch file "%s" with same content is already created', $patchFileRelativePath);
                $this->symfonyStyle->note($message);
                continue;
            }

            if (is_file($patch->getAbsolutePath())) {
                $message = sprintf('File "%s" was updated', $patchFileRelativePath);
                $this->symfonyStyle->note($message);
            } else {
                $message = sprintf('File "%s" was created', $patchFileRelativePath);
                $this->symfonyStyle->note($message);
            }

            FileSystem::write($patch->getAbsolutePath(), $patch->getDiff());

            $addedPatchFiles[] = $patch;
        }

        $this->composerPatchesConfigurationUpdater->updateComposerJson($composerExtraPatches);

        return $addedPatchFiles;
    }

    private function createPatchFilePath(
        OldAndNewFileInfo $oldAndNewFileInfo,
        InstalledPackageInfo $packageInfo
    ): string {
        $newFileInfo = $oldAndNewFileInfo->getNewFileInfo();

        $inVendorRelativeFilePath = $newFileInfo->getRelativeFilePathFromDirectory(
            $packageInfo->getInstallationDirectory()
        );

        $relativeFilePathWithoutSuffix = Strings::lower($inVendorRelativeFilePath);
        $pathFileName = Strings::webalize($relativeFilePathWithoutSuffix) . '.patch';

        return 'patches' . DIRECTORY_SEPARATOR . $pathFileName;
    }

    private function doesPatchAlreadyExist(PatchFileInfo $patch): bool
    {
        if (! is_file($patch->getAbsolutePath())) {
            return false;
        }

        return FileSystem::read($patch->getAbsolutePath()) === $patch->getDiff();
    }

    private function reportIdenticalNewAndOldFile(OldAndNewFileInfo $oldAndNewFileInfo): void
    {
        $message = sprintf(
            'Files "%s" and "%s" have the same content. Did you forgot to change it?',
            $oldAndNewFileInfo->getOldFileRelativePath(),
            $oldAndNewFileInfo->getNewFileRelativePath()
        );

        $this->symfonyStyle->warning($message);
    }
}
