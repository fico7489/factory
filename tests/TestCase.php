<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use App\Tests\Util\DataProvider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
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

        $this->client->setDefaultOptions([
            'headers' => [
                'Accept' => 'application/vnd.api+json',
            ],
        ]);

        $this->container = static::getContainer();

        $this->entityManager = $this->container->get(EntityManagerInterface::class);

        $this->dataProvider = new DataProvider($this->entityManager);

        $this->migrateDb();
    }

    protected function migrateDb(): void
    {
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);
        //$application->run(new ArrayInput(['command' => 'doctrine:schema:drop', '--force' => true, '--full-database' => 'true']));

        $metaData = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->updateSchema($metaData);

        $user = new User();
        $user->setEmail('admin@example.com');
        $user->setPassword('secret');

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    protected function asUser(User $user = null): void
    {
        /** @var JWTTokenManagerInterface $tokenManager */
        $tokenManager = $this->container->get(JWTTokenManagerInterface::class);

        $token = $tokenManager->create($user);

        $this->client->setDefaultOptions([
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
    }

    protected function asUserId( int $userId): void
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);

        $this->asUser($user);
    }
}
