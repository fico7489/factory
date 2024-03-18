<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function testService(): void
    {
        $user = new User();
        $user->setEmail('example@example.com');
        $user->setPassword('secret');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $response = $this->client->request('POST', '/api/login_check', [
            'json' => [
                'username' => 'example@example.com',
                'password' => 'secret',
            ],
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertNotNull($response->toArray()['token']);

        $response = $this->client->request('POST', '/api/login_check', [
            'json' => [
                'username' => 'example@example.com',
                'password' => 'secret2',
            ],
        ]);
        $this->assertResponseStatusCodeSame(401);
    }
}
