<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker\Template;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

final class RenderMethodTemplateExtractor
{
    /**
     * Matches $this->render('<template_name>')
     *
     * @see https://regex101.com/r/eK364x/2/
     * @var string
     */
    private const TEMPLATE_PATH_PATTERN = '#\-\>render\([\s|\n]*\'(?<template_name>[@\w\d\/\-\_\.]+[^\/])\'#ms';

    /**
     * @param SmartFileInfo[] $controllerFileInfos
     * @return string[][]
     */
    public function extractFromFileInfos(array $controllerFileInfos)
    {
        $usedTemplatePathsByControllerPath = [];
        foreach ($controllerFileInfos as $controllerFileInfo) {
            $match = Strings::match($controllerFileInfo->getContents(), self::TEMPLATE_PATH_PATTERN);
            if ($match === null) {
                continue;
            }

            /** @var string $relativeControllerFilePath */
            $relativeControllerFilePath = Strings::after($controllerFileInfo->getRealPath(), getcwd() . '/');

            $usedTemplatePathsByControllerPath[$relativeControllerFilePath][] = $match['template_name'];
        }

        // normalize array nested values
        foreach ($usedTemplatePathsByControllerPath as $key => $values) {
            sort($values);
            $usedTemplatePathsByControllerPath[$key] = array_unique($values);
        }

        return $usedTemplatePathsByControllerPath;
    }
}
