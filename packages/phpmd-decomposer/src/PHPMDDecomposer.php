<?php

declare(strict_types=1);

namespace Migrify\PHPMDDecomposer;

use Migrify\PHPMDDecomposer\PHPMDDecomposer\PHPStanPHPMDDecomposer;
use Migrify\PHPMDDecomposer\ValueObject\DecomposedFileConfigs;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PHPMDDecomposer
{
    /**
     * @var PHPStanPHPMDDecomposer
     */
    private $phpStanPHPMDDecomposer;

    public function __construct(PHPStanPHPMDDecomposer $phpStanPHPMDDecomposer)
    {
        $this->phpStanPHPMDDecomposer = $phpStanPHPMDDecomposer;
    }

    public function decompose(SmartFileInfo $smartFileInfo): DecomposedFileConfigs
    {
        $phpStanConfig = $this->phpStanPHPMDDecomposer->decompose($smartFileInfo);

        return new DecomposedFileConfigs($phpStanConfig, '', '');
    }
}
