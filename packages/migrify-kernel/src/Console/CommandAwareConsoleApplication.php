<?php

declare(strict_types=1);

namespace Migrify\MigrifyKernel\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

final class CommandAwareConsoleApplication extends Application
{
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $this->addCommands($commands);

        parent::__construct();
    }
}
