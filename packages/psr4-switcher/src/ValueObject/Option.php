<?php

declare(strict_types=1);

namespace Migrify\Psr4Switcher\ValueObject;

final class Option
{
    /**
     * @var string
     */
    public const SOURCE = 'source';

    /**
     * @var string
     */
    public const COMPOSER_JSON = 'composer-json';
}
