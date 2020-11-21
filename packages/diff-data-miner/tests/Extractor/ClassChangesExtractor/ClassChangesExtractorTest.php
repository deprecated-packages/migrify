<?php

declare(strict_types=1);

namespace Migrify\DiffDataMiner\Tests\Extractor\ClassChangesExtractor;

use Migrify\DiffDataMiner\Extractor\ClassChangesExtractor;
use Migrify\DiffDataMiner\HttpKernel\DiffDataMinerKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class ClassChangesExtractorTest extends AbstractKernelTestCase
{
    /**
     * @var ClassChangesExtractor
     */
    private $classChangesExtractor;

    protected function setUp(): void
    {
        $this->bootKernel(DiffDataMinerKernel::class);

        $this->classChangesExtractor = self::$container->get(ClassChangesExtractor::class);
    }

    public function test(): void
    {
        $classChanges = $this->classChangesExtractor->extract(__DIR__ . '/Fixture/class_changes.diff');
        $this->assertCount(3, $classChanges);
    }
}
