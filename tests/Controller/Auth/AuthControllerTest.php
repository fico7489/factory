<?php

namespace App\Tests\Controller\Auth;

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

        $response = $this->request('POST', '/api/login_check', [
            'username' => 'example@example.com',
            'password' => 'secret',
        ], 'Valid login');
        $this->assertResponseStatusCodeSame(200);
        $this->assertNotNull($response->toArray()['token']);

        $response = $this->request('POST', '/api/login_check', [
            'username' => 'example@example.com',
            'password' => 'secret2',
        ], 'Not valid login');
        $this->assertResponseStatusCodeSame(401);
    }
}
