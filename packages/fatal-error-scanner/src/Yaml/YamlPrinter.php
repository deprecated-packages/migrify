<?php

declare(strict_types=1);

namespace Migrify\FatalErrorScanner\Yaml;

use Symfony\Component\Yaml\Yaml;
use Symplify\SmartFileSystem\SmartFileSystem;

final class YamlPrinter
{
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(SmartFileSystem $smartFileSystem)
    {
        $this->smartFileSystem = $smartFileSystem;
    }

    public function printYamlToFile(array $yaml, string $targetFile): void
    {
        $yamlContent = $this->printYamlToString($yaml);
        $this->smartFileSystem->dumpFile($targetFile, $yamlContent);
    }

    private function printYamlToString(array $yaml): string
    {
        return Yaml::dump($yaml, 10, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
    }
}
