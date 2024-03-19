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
    }
}
