<?php

declare(strict_types=1);

namespace Migrify\Psr4Switcher\Configuration;

use Migrify\Psr4Switcher\Exception\ConfigurationException;
use Migrify\Psr4Switcher\ValueObject\Option;
use Symfony\Component\Console\Input\InputInterface;
use Symplify\SmartFileSystem\FileSystemGuard;

final class Psr4SwitcherConfiguration
{
    /**
     * @var string[]
     */
    private $source = [];

    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var string
     */
    private $composerJsonPath;

    public function __construct(FileSystemGuard $fileSystemGuard)
    {
        $this->fileSystemGuard = $fileSystemGuard;
    }

    /**
     * For testing
     */
    public function loadForTest(string $composerJsonPath): void
    {
        $this->composerJsonPath = $composerJsonPath;
    }

    public function loadFromInput(InputInterface $input): void
    {
        $composerJsonPath = (string) $input->getOption(Option::COMPOSER_JSON);
        if ($composerJsonPath === '') {
            throw new ConfigurationException(sprintf('Provide composer.json via "--%s"', Option::COMPOSER_JSON));
        }

        $this->fileSystemGuard->ensureFileExists($composerJsonPath, __METHOD__);

        $this->composerJsonPath = $composerJsonPath;
        $this->source = (array) $input->getArgument(Option::SOURCE);
    }

    /**
     * @return string[]
     */
    public function getSource(): array
    {
        return $this->source;
    }

    public function getComposerJsonPath(): string
    {
        return $this->composerJsonPath;
    }
}
