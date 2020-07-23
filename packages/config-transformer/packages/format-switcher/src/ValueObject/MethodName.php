<?php

declare(strict_types=1);

namespace Migrify\ConfigTransformer\FormatSwitcher\ValueObject;

final class MethodName
{
    /**
     * @var string
     */
    public const SET = 'set';

    /**
     * @var string
     */
    public const ALIAS = 'alias';

    /**
     * @var string
     */
    public const SERVICES = 'services';

    /**
     * @var string
     */
    public const PARAMETERS = 'parameters';

    /**
     * @var string
     */
    public const DEFAULTS = 'defaults';
}