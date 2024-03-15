<?php

namespace App\tests\Service\OrderPlacer\Discount;

use App\Entity\Order\OrderItem;
use App\Entity\Order\Price\PriceItem;
use App\Tests\Service\OrderPlacer\TestCaseOrderPlacer;

class ItemDiscountTest extends TestCaseOrderPlacer
{
    public function testService(): void
    {
        $this->dataProvider->createUser();
        $category = $this->dataProvider->createCategory('Monitor');
        $this->dataProvider->createProduct(50, 'test', $category);

        $order = $this->orderPlacer->placeOrder($this->dataProvider->getOrderData([1 => 1]));

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
        $this->assertEquals(PriceItem::TYPE_PRODUCT, $orderItem->getPriceItem()->getType());

        $this->assertCount(1, $order->getOrderItems());
        $this->assertEquals(50, $order->getSubtotal());
        $this->assertEquals(-5, $order->getDiscount());
        $this->assertEquals(11.25, $order->getTax());
        $this->assertEquals(56.25, $order->getTotal());
    }
}