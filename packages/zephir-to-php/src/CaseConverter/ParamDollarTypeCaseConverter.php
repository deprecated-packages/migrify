<?php

declare(strict_types=1);

namespace Migrify\ZephirToPhp\CaseConverter;

use Migrify\ZephirToPhp\Contract\CaseConverter\CaseConverterInterface;
use Nette\Utils\Strings;

final class ParamDollarTypeCaseConverter implements CaseConverterInterface
{
    /**
     * @var string
     */
    private const PARAMS = 'params';

    public function convertContent(string $content): string
    {
        return Strings::replace(
            $content,
            '#(?<pre_content>function \w+\()(?<params>.*?)(?<after_content>\))#',
            function (array $match) {
                $params = explode(',', $match[self::PARAMS]);
                foreach ($params as $key => $param) {
                    $params[$key] = Strings::replace($param, '#(\w+)$#', '$$1');
                }

                $match[self::PARAMS] = implode(',', $params);

                return $match['pre_content'] . $match[self::PARAMS] . $match['after_content'];
            }
        );
    }
}
