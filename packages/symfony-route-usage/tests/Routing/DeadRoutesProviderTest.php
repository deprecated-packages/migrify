<?php

declare(strict_types=1);

namespace Migrify\SymfonyRouteUsage\Tests\Routing;

use Migrify\SymfonyRouteUsage\Entity\RouteVisit;
use Migrify\SymfonyRouteUsage\EntityRepository\RouteVisitRepository;
use Migrify\SymfonyRouteUsage\Routing\DeadRoutesProvider;
use Migrify\SymfonyRouteUsage\Tests\Helper\DatabaseLoaderHelper;
use Migrify\SymfonyRouteUsage\Tests\HttpKernel\SymfonyRouteUsageKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class DeadRoutesProviderTest extends AbstractKernelTestCase
{
    /**
     * @var DeadRoutesProvider
     */
    private $deadRoutesProvider;

    /**
     * @var RouteVisitRepository
     */
    private $routeVisitRepository;

    protected function setUp(): void
    {
        $this->bootKernel(SymfonyRouteUsageKernel::class);

        $databaseLoaderHelper = new DatabaseLoaderHelper(self::$container);
        $databaseLoaderHelper->disableDoctrineLogger();
        $databaseLoaderHelper->createDatabase();
        $this->deadRoutesProvider = self::$container->get(DeadRoutesProvider::class);
        $this->routeVisitRepository = self::$container->get(RouteVisitRepository::class);
    }

    public function test(): void
    {
        $routeVisit = new RouteVisit('acme_privacy', "{'route':'params'}", 'SomeController', 'some_hash');
        $this->routeVisitRepository->save($routeVisit);

        $deadRoutes = $this->deadRoutesProvider->provide();
        $this->assertCount(0, $deadRoutes);

        $this->assertArrayNotHasKey('acme_privacy', $deadRoutes);
        $this->assertArrayNotHasKey('acme_privacy.es', $deadRoutes);
    }
}
