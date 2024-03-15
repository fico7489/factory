<?php

namespace App\Tests\Service;

use App\Entity\Order\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use App\Service\Discount\Applicator\DiscountApplicator;
use App\Service\Order\OrderCreator;
use App\Service\Order\OrderItemCreator;
use App\Service\Order\ProductPriceCreator;
use App\Service\OrderPlacer;
use App\Tests\TestCase;

class OrderApplicator2Test extends TestCase
{
    public function testService(): void
    {
        /** @var OrderPlacer $orderApplicator */
        $orderApplicator = $this->container->get(OrderPlacer::class);

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

        // create order
        /** @var OrderCreator $orderCreator */
        $orderCreator = $this->container->get(OrderCreator::class);
        $order = $orderCreator->create($user);
        $this->entityManager->refresh($order);

        // create order item
        /** @var OrderItemCreator $orderItemCreator */
        $orderItemCreator = $this->container->get(OrderItemCreator::class);
        $orderItem = $orderItemCreator->create($order, $product, 2);

        /** @var ProductPriceCreator $productPriceCreator */
        $productPriceCreator = $this->container->get(ProductPriceCreator::class);

        $order = $productPriceCreator->create($order);
        /** @var OrderItem $orderItem */
        $orderItem = $order->getOrderItems()[0];
        $this->assertEquals(10, $orderItem->getPrice());
        $this->assertEquals(10, $orderItem->getPriceAdjusted());

        $this->entityManager->refresh($order);
        $this->createContractList($user, $product, 9);
        $order = $productPriceCreator->create($order);
        /** @var OrderItem $orderItem */
        $orderItem = $order->getOrderItems()[0];
        $this->assertEquals(10, $orderItem->getPrice());
        $this->assertEquals(9, $orderItem->getPriceAdjusted());

        /** @var DiscountApplicator $discountCreator */
        $discountCreator = $this->container->get(DiscountApplicator::class);
        $discountCreator->apply($order);

        $this->entityManager->refresh($order);
        /** @var OrderItem $orderItem */
        $orderItem = $order->getOrderItems()[0];
        $this->assertEquals(0, $orderItem->getDiscountGlobal());
        $this->assertEquals(0, $orderItem->getDiscountItem());
    }

    private function createContractList(User $user, Product $product, float $price): Product\ContractList
    {
        $contractList = new Product\ContractList();

        $contractList->setSku($product->getSku());
        $contractList->setPrice($price);
        $contractList->setUser($user);

        $this->entityManager->persist($contractList);
        $this->entityManager->flush();

        return $contractList;
    }
}
