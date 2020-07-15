<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker\Tests\Template\PossibleTemplatePathsResolver;

use Migrify\TemplateChecker\HttpKernel\TemplateCheckerKernel;
use Migrify\TemplateChecker\Template\TemplatePathsResolver;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class PossibleTemplatePathsResolverTest extends AbstractKernelTestCase
{
    /**
     * @var TemplatePathsResolver
     */
    private $templatePathsResolver;

    protected function setUp(): void
    {
        self::bootKernel(TemplateCheckerKernel::class);

        $this->templatePathsResolver = self::$container->get(TemplatePathsResolver::class);
    }

    public function test(): void
    {
        $templatePaths = $this->templatePathsResolver->resolveFromDirectories([__DIR__ . '/../../SomeBundle']);
        $this->assertCount(1, $templatePaths);

        $this->assertSame(['@RealClass/FirstName/SecondName/template.html.twig'], $templatePaths);
    }
}
