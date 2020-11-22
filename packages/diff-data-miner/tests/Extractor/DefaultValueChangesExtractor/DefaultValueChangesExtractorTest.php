<?php

declare(strict_types=1);

namespace Migrify\DiffDataMiner\Tests\Extractor\DefaultValueChangesExtractor;

use Migrify\DiffDataMiner\Extractor\DefaultValueChangesExtractor;
use Migrify\DiffDataMiner\HttpKernel\DiffDataMinerKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class DefaultValueChangesExtractorTest extends AbstractKernelTestCase
{
    /**
     * @var DefaultValueChangesExtractor
     */
    private $defaultValueChangesExtractor;

    protected function setUp(): void
    {
        $this->bootKernel(DiffDataMinerKernel::class);

        $this->defaultValueChangesExtractor = self::$container->get(DefaultValueChangesExtractor::class);
    }

    public function test(): void
    {
        $defaultValueChanges = $this->defaultValueChangesExtractor->extract(
            __DIR__ . '/Fixture/default_value_changes.diff'
        );
        $this->assertCount(1, $defaultValueChanges);
    }
}
