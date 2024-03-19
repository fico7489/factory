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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TestCase extends ApiTestCase
{
    private static ?string $jwtToken = null;

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
        // $application->run(new ArrayInput(['command' => 'doctrine:schema:drop', '--force' => true, '--full-database' => 'true']));

        $metaData = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->updateSchema($metaData);

        $user = new User();
        $user->setEmail('admin@example.com');
        $user->setPassword('secret');

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    protected function asUser(?User $user = null): void
    {
        /** @var JWTTokenManagerInterface $tokenManager */
        $tokenManager = $this->container->get(JWTTokenManagerInterface::class);

        $jwtToken = $tokenManager->create($user);

        self::$jwtToken = $jwtToken;

        $this->client->setDefaultOptions([
            'headers' => [
                'Authorization' => 'Bearer '.$jwtToken,
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
    }

    protected function request($method, $url, $json = [], $description = ''): ResponseInterface
    {
        $this->storeDocumentation($method, $url, $json, $description);

        return $this->client->request($method, $url, [
            'json' => $json,
        ]);
    }

    private function storeDocumentation($method, $url, $json = [], $description = ''): void
    {
        $parameterBag = $this->container->get(ParameterBagInterface::class);

        $curl = 'curl --location --request '.$method.' '.$parameterBag->get('api_url').$url.
            " --header 'Content-Type: application/json' ".
            " --header 'Authorization: Bearer ".self::$jwtToken."' ".
            ' --data '."'".json_encode($json)."'";

        $path = 'api_documentation/'.str_replace('\\', '_', static::class).'.md';

        $string =
            "\n"
            .$description
            ."\n\n"
            .$curl
            ."\n"
            .'------------------------------------';

        file_put_contents($path, $string, FILE_APPEND);
    }
}
