<?php

declare(strict_types=1);

namespace Migrify\ConfigFeatureBumper\Utils;

final class MigrifyArrays
{
    /**
     * @param mixed[] $items
     */
    public function hasOnlyKey(array $items, string $key): bool
    {
        if (count($items) !== 1) {
            return false;
        }

        return isset($items[$key]);
    }
}
