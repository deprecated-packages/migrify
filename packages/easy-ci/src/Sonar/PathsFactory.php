<?php

declare(strict_types=1);

namespace Migrify\EasyCI\Sonar;

use Symplify\SmartFileSystem\SmartFileInfo;

final class PathsFactory
{
    /**
     * @param SmartFileInfo[] $directoryFileInfos
     */
    public function createFromDirectories(array $directoryFileInfos): string
    {
        $relativePaths = [];
        foreach ($directoryFileInfos as $directoryFileInfo) {
            $relativePaths[] = $directoryFileInfo->getRelativeFilePathFromCwd();
        }

        sort($relativePaths);

        return implode(',', $relativePaths);
    }
}
