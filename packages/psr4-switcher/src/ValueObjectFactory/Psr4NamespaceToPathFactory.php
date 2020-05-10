<?php

declare(strict_types=1);

namespace Migrify\Psr4Switcher\ValueObjectFactory;

use Migrify\Psr4Switcher\Configuration\Psr4SwitcherConfiguration;
use Migrify\Psr4Switcher\Utils\MigrifyStrings;
use Migrify\Psr4Switcher\ValueObject\Psr4NamespaceToPath;
use Nette\Utils\Strings;

final class Psr4NamespaceToPathFactory
{
    /**
     * @var MigrifyStrings
     */
    private $migrifyStrings;

    /**
     * @var Psr4SwitcherConfiguration
     */
    private $psr4SwitcherConfiguration;

    public function __construct(MigrifyStrings $migrifyStrings, Psr4SwitcherConfiguration $psr4SwitcherConfiguration)
    {
        $this->migrifyStrings = $migrifyStrings;
        $this->psr4SwitcherConfiguration = $psr4SwitcherConfiguration;
    }

    public function createFromClassAndFile(string $class, string $file): ?Psr4NamespaceToPath
    {
        $sharedSuffix = $this->migrifyStrings->findSharedSuffix($class . '.php', $file);

        $uniqueFilePath = $this->migrifyStrings->subtractFromRight($file, $sharedSuffix);
        $uniqueNamespace = $this->migrifyStrings->subtractFromRight($class . '.php', $sharedSuffix);

        // fallback for identical namespace + file directory
        if ($uniqueNamespace === '') {
            // shorten shared suffix by "Element/"
            $sharedSuffix = '/' . Strings::after($sharedSuffix, '/');

            $uniqueFilePath = $this->migrifyStrings->subtractFromRight($file, $sharedSuffix);
            $uniqueNamespace = $this->migrifyStrings->subtractFromRight($class . '.php', $sharedSuffix);
        }

        $commonFilePathPrefix = Strings::findPrefix(
            [$uniqueFilePath, $this->psr4SwitcherConfiguration->getComposerJsonPath()]
        );

        $relativeDirectory = $this->migrifyStrings->subtractFromLeft($uniqueFilePath, $commonFilePathPrefix);

        if ($uniqueNamespace === '' || $relativeDirectory === '') {
            // skip
            return null;
        }

        return new Psr4NamespaceToPath($uniqueNamespace, $relativeDirectory);
    }
}
