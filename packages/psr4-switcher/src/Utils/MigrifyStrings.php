<?php

declare(strict_types=1);

namespace Migrify\Psr4Switcher\Utils;

use Nette\Utils\Strings;

final class MigrifyStrings
{
    /**
     * Same as ↓, just for the suffix
     * @see \Nette\Utils\Strings::findPrefix()
     */
    public function findSharedSuffix(string $firstString, string $secondString): string
    {
        $max = strlen($secondString);

        $sharedSuffix = '';
        $firstString = $this->normalizePath($firstString);
        $secondString = $this->normalizePath($secondString);

        for ($i = 1; $i < $max; ++$i) {
            $possibleSuffix = Strings::substring($secondString, -$i);
            if (Strings::endsWith($firstString, $possibleSuffix)) {
                $sharedSuffix = $possibleSuffix;
                continue;
            }

            if (! isset($firstString[$i])) {
                break;
            }

            if (! isset($secondString[$i])) {
                break;
            }
        }

        return $sharedSuffix;
    }

    public function normalizePath(string $firstString): string
    {
        return Strings::replace($firstString, '#\\\\#', '/');
    }

    public function subtractFromRight(string $mainString, string $stringToSubtract): string
    {
        return Strings::substring($mainString, 0, -strlen($stringToSubtract));
    }

    public function subtractFromLeft(string $mainString, string $stringToSubtract): string
    {
        return Strings::substring($mainString, strlen($stringToSubtract));
    }
}
