<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\Contract;

use PhpParser\Node\Stmt\Expression;

interface NestedCaseConverterInterface
{
    public function match(string $rootKey, $subKey): bool;

    public function convertToMethodCall($key, $values): Expression;
}
