<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\Console;

use Migrify\StaticDetector\Console\Command\DetectCommand;
use Symfony\Component\Console\Application;

final class StaticDetectorApplication extends Application
{
    public function __construct(DetectCommand $detectCommand)
    {
        $this->add($detectCommand);

        parent::__construct();
    }
}
