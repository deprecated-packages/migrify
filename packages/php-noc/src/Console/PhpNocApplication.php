<?php

declare(strict_types=1);

namespace Migrify\PhpNoc\Console;

use Migrify\PhpNoc\Command\AnalyseCommand;
use Symfony\Component\Console\Application;

final class PhpNocApplication extends Application
{
    public function __construct(AnalyseCommand $analyseCommand)
    {
        parent::__construct();

        $this->add($analyseCommand);

        /** @var string $commandName */
        $commandName = $analyseCommand->getName();
        $this->setDefaultCommand($commandName, true);
    }
}
