<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Entity\User;
use App\Service\OrderPlacer;
use App\Tests\TestCase;

class OrderPlacerTest extends TestCase
{
    public function testService(): void
    {
        /** @var OrderPlacer $orderPlacer */
        $orderPlacer = $this->container->get(OrderPlacer::class);

        $orderData = [
            'user_id' => 1,
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                ],
            ],
        ];

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('secret');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $product = new Product();
        $product->setName('test');
        $product->setPrice(120);
        $product->setSku('test');
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $order = $orderPlacer->placeOrder($orderData);

        dd($order->getDiscount());

        $this->assertEquals(1, 1);
    }
}
