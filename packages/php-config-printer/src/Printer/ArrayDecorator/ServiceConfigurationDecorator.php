<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\Printer\ArrayDecorator;

use Migrify\PhpConfigPrinter\Reflection\ConstantNameFromValueResolver;

final class ServiceConfigurationDecorator
{
    /**
     * @var ConstantNameFromValueResolver
     */
    private $constantNameFromValueResolver;

    public function __construct(ConstantNameFromValueResolver $constantNameFromValueResolver)
    {
        $this->constantNameFromValueResolver = $constantNameFromValueResolver;
    }

    /**
     * @param mixed|mixed[] $configuration
     * @return mixed|mixed[]
     */
    public function decorate($configuration, string $class)
    {
        if (! is_array($configuration)) {
            return $configuration;
        }

        foreach ($configuration as $key => $subValue) {
            $constantName = $this->constantNameFromValueResolver->resolveFromValueAndClass($key, $class);
            if ($constantName === null) {
                continue;
            }

            unset($configuration[$key]);

            $classConstantReference = $class . '::' . $constantName;
            $configuration[$classConstantReference] = $subValue;
        }

        return $configuration;
    }
}
