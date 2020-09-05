<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\CurrentProvider;

use Symplify\SmartFileSystem\SmartFileInfo;

final class CurrentFileInfoProvider
{
    /**
     * @var SmartFileInfo
     */
    private $smartFileInfo;

    public function setCurrentFileInfo(SmartFileInfo $smartFileInfo): void
    {
        $this->smartFileInfo = $smartFileInfo;
    }

    public function getSmartFileInfo(): SmartFileInfo
    {
        return $this->smartFileInfo;
    }
}
