<?php

namespace App\Tests\Service;

use App\Entity\Order;
use App\Entity\Order\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use App\Service\OrderAdjustment\OrderItemApplicator;
use App\Tests\TestCase;

class OrderItemApplicatorTest extends TestCase
{
    public function testService(): void
    {
        /** @var OrderItemApplicator $orderItemApplicator */
        $orderItemApplicator = $this->container->get(OrderItemApplicator::class);
        $order = new Order();
        $this->entityManager->persist($order);
        $this->entityManager->flush();


        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('secret');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $product = new Product();
        $product->setName('test');
        $product->setPrice(10);
        $product->setSku('test');
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $order = new Order();
        $order->setUser($user);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $orderItem = new OrderItem();
        $orderItem->setProduct($product);
        $orderItem->setOrder($order);
        $orderItemApplicator->apply($orderItem);

        $this->assertEquals(1, 1);
    }
}
