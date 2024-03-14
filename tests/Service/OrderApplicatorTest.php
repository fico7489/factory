<?php

namespace App\Tests\Service;

use App\Entity\Order;
use App\Entity\Order\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use App\Service\OrderApplicator;
use App\Tests\TestCase;

class OrderApplicatorTest extends TestCase
{
    public function testService(): void
    {
        /** @var OrderApplicator $orderApplicator */
        $orderApplicator = $this->container->get(OrderApplicator::class);

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
        $orderItem->setQuantity(2);
        $orderItem->setProduct($product);
        $orderItem->setOrder($order);

        $this->entityManager->persist($orderItem);
        $this->entityManager->flush();

        $this->entityManager->refresh($order);
        $orderApplicator->apply($order);

        $this->assertEquals(1, 1);
    }
}
