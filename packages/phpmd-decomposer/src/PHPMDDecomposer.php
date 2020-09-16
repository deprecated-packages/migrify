<?php

declare(strict_types=1);

namespace Migrify\PHPMDDecomposer;

use Migrify\PHPMDDecomposer\PHPMDDecomposer\PHPStanConfigFactory;
use Migrify\PHPMDDecomposer\ValueObject\DecomposedFileConfigs;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PHPMDDecomposer
{
    /**
     * @var PHPStanConfigFactory
     */
    private $phpStanConfigFactory;

    public function __construct(PHPStanConfigFactory $phpStanConfigFactory)
    {
        $this->phpStanConfigFactory = $phpStanConfigFactory;
    }

    public function decompose(SmartFileInfo $smartFileInfo): DecomposedFileConfigs
    {
        $phpStanConfig = $this->phpStanConfigFactory->decompose($smartFileInfo);

        return new DecomposedFileConfigs($phpStanConfig, '', '');
    }
}
