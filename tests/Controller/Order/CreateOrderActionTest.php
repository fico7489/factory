<?php

namespace App\Tests\Controller\Order;

use App\Tests\TestCase;

class CreateOrderActionTest extends TestCase
{
    public function testService(): void
    {
        $user = $this->dataProvider->createUser();
        $product = $this->dataProvider->createProduct(10);

        $this->asUser($user);
        $response = $this->request('POST', '/api/orders', [
            'items' => [
                $product->getId() => 1,
            ],
        ], 'Create order');

        $this->assertEquals(10, $response->toArray()['data']['attributes']['subtotal']);

        // with address
        $response = $this->request('POST', '/api/orders', [
            'items' => [
                $product->getId() => 1,
            ],
            'addressAddress' => 'tester1234',
            'addressCity' => 'tester1235',
            'addressCountry' => 'tester1236',
            'addressPhone' => 'tester1237',
        ], 'Create order2');

        $this->assertEquals('tester1234', $response->toArray()['data']['attributes']['addressAddress']);
        $this->assertEquals('tester1235', $response->toArray()['data']['attributes']['addressCity']);
        $this->assertEquals('tester1236', $response->toArray()['data']['attributes']['addressCountry']);
        $this->assertEquals('tester1237', $response->toArray()['data']['attributes']['addressPhone']);
    }
}
