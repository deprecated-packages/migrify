<?php

declare(strict_types=1);

namespace Migrify\ConfigClarity\Tests\Clarifier\NeonYamlConfigClarifier;

use Iterator;
use Migrify\ConfigClarity\Clarifier\NeonYamlConfigClarifier;
use Migrify\ConfigClarity\HttpKernel\ConfigClarityKernel;
use Migrify\ConfigClarity\Tests\StaticFixtureProvider;
use Nette\Utils\Strings;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NeonYamlConfigClarifierTest extends AbstractKernelTestCase
{
    /**
     * @var NeonYamlConfigClarifier
     */
    private $neonYamlConfigClarifier;

    protected function setUp(): void
    {
        $this->bootKernel(ConfigClarityKernel::class);
        $this->neonYamlConfigClarifier = self::$container->get(NeonYamlConfigClarifier::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath): void
    {
        $fileInfo = new SmartFileInfo($filePath);

        [$inputContent, $expectedContent] = Strings::split($fileInfo->getContents(), "#-----\n#");

        $changedContent = $this->neonYamlConfigClarifier->clarify($inputContent, $fileInfo->getSuffix());
        $this->assertSame($expectedContent . PHP_EOL, $changedContent);
    }

    public function provideData(): Iterator
    {
        return StaticFixtureProvider::yieldFilesFromDirectory(__DIR__ . '/Source');
    }
}
