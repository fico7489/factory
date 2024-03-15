<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Tests\Util\DataProvider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\Container;

class TestCase extends ApiTestCase
{
    protected Client $client;
    protected Container $container;
    protected EntityManagerInterface $entityManager;
    protected DataProvider $dataProvider;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        $this->container = static::getContainer();

        $this->entityManager = $this->container->get(EntityManagerInterface::class);

        $this->dataProvider = new DataProvider($this->entityManager);

        $this->migrateDb();
    }

    private function migrateDb(): void
    {
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);
        $application->run(new ArrayInput(['command' => 'doctrine:schema:drop', '--force' => true, '--full-database' => 'true']));

        $metaData = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->updateSchema($metaData);
    }
}
