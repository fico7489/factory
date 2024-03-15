<?php

namespace App\Tests\Service\OrderPlacer;

use App\Entity\Order\OrderItem;
use App\Entity\Order\Price\OrderItemPrice;

class BasicTest extends TestCaseOrderPlacer
{
    public function testService(): void
    {
        $user = $this->dataProvider->createUser();
        $product = $this->dataProvider->createProduct(40, 'test');

        $order = $this->orderPlacer->placeOrder($this->dataProvider->getOrderData($user, [$product->getId() => 2]));

        /** @var OrderItem $orderItem */
        $orderItem = $order->getOrderItems()[0];

        $this->assertEquals(2, $orderItem->getQuantity());
        $this->assertEquals(40, $orderItem->getPrice());
        $this->assertEquals(40, $orderItem->getPriceAdjusted());
        $this->assertEquals(80, $orderItem->getSubtotal());
        $this->assertEquals(0, $orderItem->getDiscountGlobal());
        $this->assertEquals(0, $orderItem->getDiscountItem());
        $this->assertEquals(0, $orderItem->getDiscount());
        $this->assertEquals(20, $orderItem->getTax());
        $this->assertEquals(100, $orderItem->getTotal());
        $this->assertEquals(OrderItemPrice::TYPE_PRODUCT, $orderItem->getPriceItem()->getType());

        $this->assertCount(1, $order->getOrderItems());
        $this->assertEquals(80, $order->getSubtotal());
        $this->assertEquals(0, $order->getDiscount());
        $this->assertEquals(20, $order->getTax());
        $this->assertEquals(100, $order->getTotal());
    }
}
