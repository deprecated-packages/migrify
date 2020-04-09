<?php

declare(strict_types=1);

namespace Migrify\Psr4Switcher\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

final class Psr4SwitcherApplication extends Application
{
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        foreach ($commands as $command) {
            $this->add($command);
        }

        parent::__construct();
    }
}
