<?php

namespace App\tests\Service\OrderPlacer\Discount;

use App\Entity\Order\OrderItem;
use App\Entity\Order\Price\OrderItemPrice;
use App\Tests\Service\OrderPlacer\TestCaseOrderPlacer;

class CombinedDiscountTest extends TestCaseOrderPlacer
{
    public function testService(): void
    {
        $this->dataProvider->createUser();
        $category = $this->dataProvider->createCategory('Monitor');
        $product = $this->dataProvider->createProduct(50, 'test', $category);
        $product2 = $this->dataProvider->createProduct(60, 'test2');

        $order = $this->orderPlacer->placeOrder($this->dataProvider->getOrderData([$product->getId() => 1, $product2->getId() => 1]));

        /** @var OrderItem $orderItem */
        $orderItem = $order->getOrderItems()[0];

        $this->assertEquals(1, $orderItem->getQuantity());
        $this->assertEquals(50, $orderItem->getPrice());
        $this->assertEquals(50, $orderItem->getPriceAdjusted());
        $this->assertEquals(50, $orderItem->getSubtotal());
        $this->assertEquals(-5, $orderItem->getDiscountGlobal());
        $this->assertEquals(-5, $orderItem->getDiscountItem());
        $this->assertEquals(-10, $orderItem->getDiscount());
        $this->assertEquals(10, $orderItem->getTax());
        $this->assertEquals(50, $orderItem->getTotal());
        $this->assertEquals(OrderItemPrice::TYPE_PRODUCT, $orderItem->getPriceItem()->getType());

        /** @var OrderItem $orderItem2 */
        $orderItem2 = $order->getOrderItems()[1];

        $this->assertEquals(1, $orderItem2->getQuantity());
        $this->assertEquals(60, $orderItem2->getPrice());
        $this->assertEquals(60, $orderItem2->getPriceAdjusted());
        $this->assertEquals(60, $orderItem2->getSubtotal());
        $this->assertEquals(-5, $orderItem2->getDiscountGlobal());
        $this->assertEquals(0, $orderItem2->getDiscountItem());
        $this->assertEquals(-5, $orderItem2->getDiscount());
        $this->assertEquals(13.75, $orderItem2->getTax());
        $this->assertEquals(68.75, $orderItem2->getTotal());
        $this->assertEquals(OrderItemPrice::TYPE_PRODUCT, $orderItem2->getPriceItem()->getType());

        $this->assertCount(2, $order->getOrderItems());
        $this->assertEquals(110, $order->getSubtotal());
        $this->assertEquals(-15, $order->getDiscount());
        $this->assertEquals(23.75, $order->getTax());
        $this->assertEquals(118.75, $order->getTotal());
    }
}
