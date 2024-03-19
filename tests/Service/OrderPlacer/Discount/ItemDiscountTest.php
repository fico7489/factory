<?php

namespace App\tests\Service\OrderPlacer\Discount;

use App\Entity\Order\OrderItem;
use App\Entity\Order\Price\OrderItemPrice;
use App\Tests\Service\OrderPlacer\TestCaseOrderPlacer;

class ItemDiscountTest extends TestCaseOrderPlacer
{
    public function testService(): void
    {
        $user = $this->dataProvider->createUser();
        $category = $this->dataProvider->createCategory('Monitor');
        $product = $this->dataProvider->createProduct(50, 'test', 'test', [$category]);

        $order = $this->orderPlacer->placeOrder($user, [$product->getId() => 1]);

        /** @var OrderItem $orderItem */
        $orderItem = $order->getOrderItems()[0];

        $this->assertEquals(1, $orderItem->getQuantity());
        $this->assertEquals(50, $orderItem->getPrice());
        $this->assertEquals(50, $orderItem->getPriceAdjusted());
        $this->assertEquals(50, $orderItem->getSubtotal());
        $this->assertEquals(0, $orderItem->getDiscountGlobal());
        $this->assertEquals(-5, $orderItem->getDiscountItem());
        $this->assertEquals(-5, $orderItem->getDiscount());
        $this->assertEquals(11.25, $orderItem->getTax());
        $this->assertEquals(56.25, $orderItem->getTotal());
        $this->assertEquals(OrderItemPrice::TYPE_PRODUCT, $orderItem->getPriceItem()->getType());

        $this->assertCount(1, $order->getOrderItems());
        $this->assertEquals(50, $order->getSubtotal());
        $this->assertEquals(-5, $order->getDiscount());
        $this->assertEquals(11.25, $order->getTax());
        $this->assertEquals(56.25, $order->getTotal());
    }
}
