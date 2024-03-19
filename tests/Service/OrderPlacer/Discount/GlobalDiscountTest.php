<?php

namespace App\tests\Service\OrderPlacer\Discount;

use App\Entity\Order\OrderItem;
use App\Entity\Order\Price\OrderItemPrice;
use App\Tests\Service\OrderPlacer\TestCaseOrderPlacer;

class GlobalDiscountTest extends TestCaseOrderPlacer
{
    public function testService(): void
    {
        $user = $this->dataProvider->createUser();
        $product = $this->dataProvider->createProduct(50, 'test');

        $order = $this->orderPlacer->placeOrder($user, [$product->getId() => 3]);

        /** @var OrderItem $orderItem */
        $orderItem = $order->getOrderItems()[0];

        $this->assertEquals(3, $orderItem->getQuantity());
        $this->assertEquals(50, $orderItem->getPrice());
        $this->assertEquals(50, $orderItem->getPriceAdjusted());
        $this->assertEquals(150, $orderItem->getSubtotal());
        $this->assertEquals(-10, $orderItem->getDiscountGlobal());
        $this->assertEquals(0, $orderItem->getDiscountItem());
        $this->assertEquals(-10, $orderItem->getDiscount());
        $this->assertEquals(35, $orderItem->getTax());
        $this->assertEquals(175, $orderItem->getTotal());
        $this->assertEquals(OrderItemPrice::TYPE_PRODUCT, $orderItem->getOrderPriceItem()->getType());

        $this->assertCount(1, $order->getOrderItems());
        $this->assertEquals(150, $order->getSubtotal());
        $this->assertEquals(-10, $order->getDiscount());
        $this->assertEquals(35, $order->getTax());
        $this->assertEquals(175, $order->getTotal());
    }
}
