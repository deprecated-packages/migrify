<?php

declare(strict_types=1);

namespace Migrify\SnifferFixerToECS;

use Migrify\PhpConfigPrinter\ValueObject\YamlKey;
use Symplify\EasyCodingStandard\Configuration\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

final class SymfonyConfigFormatFactory
{
    /**
     * @param string[] $sniffClasses
     * @param string[] $setsParameter
     * @param array<string, string|string[]|null> $skipParameter
     * @param string[] $excludePathsParameter
     * @return mixed[]
     */
    public function createSymfonyConfigFormat(
        array $sniffClasses,
        array $setsParameter,
        array $skipParameter,
        array $excludePathsParameter,
        array $pathsParameter
    ): array {
        $yaml = [];

        if ($sniffClasses !== []) {
            $yaml[YamlKey::SERVICES] = $sniffClasses;
        }

        if ($pathsParameter !== []) {
            $yaml[YamlKey::PARAMETERS][Option::class . '::PATHS'] = $pathsParameter;
        }

        $setsParameter = array_unique($setsParameter);
        foreach ($setsParameter as $set) {
            $yaml[YamlKey::PARAMETERS][Option::class . '::SETS'][] = SetList::class . '::' . $set;
        }

        if ($excludePathsParameter !== []) {
            $yaml[YamlKey::PARAMETERS][Option::class . '::EXCLUDE_PATHS'] = $excludePathsParameter;
        }

        if ($skipParameter !== []) {
            $yaml[YamlKey::PARAMETERS][Option::class . '::SKIP'] = $skipParameter;
        }

        return $yaml;
    }
}
