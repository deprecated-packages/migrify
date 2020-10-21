<?php

declare(strict_types=1);

namespace Migrify\SymfonyRouteUsage\Tests\Helper;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Migrify\SymfonyRouteUsage\Entity\RouteVisit;
use Psr\Container\ContainerInterface;

final class DatabaseLoaderHelper
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function disableDoctrineLogger(): void
    {
        // @see https://stackoverflow.com/a/35222045/1348344
        // disable Doctrine logs in tests output
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        $entityManager->getConfiguration();

        $connection = $entityManager->getConnection();

        /** @var Configuration $configuration */
        $configuration = $connection->getConfiguration();
        $configuration->setSQLLogger(null);
    }

    /**
     * create database, basically same as: "bin/console doctrine:database:create" in normal Symfony app
     */
    public function createDatabase(): void
    {
        /** @var Connection $connection */
        $connection = $this->container->get('doctrine.dbal.default_connection');
        $databaseName = $this->container->getParameter('database_name');

        $existingDatabases = $connection->getSchemaManager()
            ->listDatabases();
        if (! in_array($databaseName, $existingDatabases, true)) {
            $databaseName = $connection->getDatabasePlatform()
                ->quoteSingleIdentifier($databaseName);
            // somehow broken on my pc, see https://github.com/doctrine/dbal/pull/2671
            $connection->getSchemaManager()
                ->createDatabase($databaseName);
        }

        $connection->delete('route_visit', [
            '1' => '1',
        ]);
        $rv = new RouteVisit('acme_privacy', 'Symfony\Bundle\FrameworkBundle\Controller\TemplateController', '', '');
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        $entityManager->persist($rv);
        $entityManager->flush();
    }
}
