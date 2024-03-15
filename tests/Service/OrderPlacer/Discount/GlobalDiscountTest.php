<?php

namespace App\tests\Service\OrderPlacer\Discount;

use App\Entity\Order\OrderItem;
use App\Entity\Order\Price\PriceItem;
use App\Tests\Service\OrderPlacer\TestCaseOrderPlacer;

class GlobalDiscountTest extends TestCaseOrderPlacer
{
    public function testService(): void
    {
        $this->dataProvider->createUser();
        $this->dataProvider->createProduct(50, 'test');

        $order = $this->orderPlacer->placeOrder($this->dataProvider->getOrderData([1 => 3]));

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
        $this->assertEquals(PriceItem::TYPE_PRODUCT, $orderItem->getPriceItem()->getType());

        $this->assertCount(1, $order->getOrderItems());
        $this->assertEquals(150, $order->getSubtotal());
        $this->assertEquals(-10, $order->getDiscount());
        $this->assertEquals(35, $order->getTax());
        $this->assertEquals(175, $order->getTotal());
    }
}
