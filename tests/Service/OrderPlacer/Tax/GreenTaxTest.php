<?php

use App\Entity\Order\OrderItem;
use App\Entity\Order\Price\OrderItemPrice;
use App\Tests\Service\OrderPlacer\TestCaseOrderPlacer;

class GreenTaxTest extends TestCaseOrderPlacer
{
    public function testService(): void
    {
        $user = $this->dataProvider->createUser();
        $category = $this->dataProvider->createCategory('Charger');
        $product = $this->dataProvider->createProduct(200, 'test', $category);
        $product2 = $this->dataProvider->createProduct(300, 'test2');

        $order = $this->orderPlacer->placeOrder($user, [$product->getId() => 1, $product2->getId() => 1]);

        /** @var OrderItem $orderItem */
        $orderItem = $order->getOrderItems()[0];

        $this->assertEquals(1, $orderItem->getQuantity());
        $this->assertEquals(200, $orderItem->getPrice());
        $this->assertEquals(200, $orderItem->getPriceAdjusted());
        $this->assertEquals(200, $orderItem->getSubtotal());
        $this->assertEquals(-5, $orderItem->getDiscountGlobal());
        $this->assertEquals(0, $orderItem->getDiscountItem());
        $this->assertEquals(-5, $orderItem->getDiscount());
        $this->assertEquals(68.25, $orderItem->getTax());
        $this->assertEquals(263.25, $orderItem->getTotal());
        $this->assertEquals(OrderItemPrice::TYPE_PRODUCT, $orderItem->getPriceItem()->getType());

        /** @var OrderItem $orderItem2 */
        $orderItem2 = $order->getOrderItems()[1];

        $this->assertEquals(1, $orderItem2->getQuantity());
        $this->assertEquals(300, $orderItem2->getPrice());
        $this->assertEquals(300, $orderItem2->getPriceAdjusted());
        $this->assertEquals(300, $orderItem2->getSubtotal());
        $this->assertEquals(-5, $orderItem2->getDiscountGlobal());
        $this->assertEquals(0, $orderItem2->getDiscountItem());
        $this->assertEquals(-5, $orderItem2->getDiscount());
        $this->assertEquals(73.75, $orderItem2->getTax());
        $this->assertEquals(368.75, $orderItem2->getTotal());
        $this->assertEquals(OrderItemPrice::TYPE_PRODUCT, $orderItem2->getPriceItem()->getType());

        $this->assertCount(2, $order->getOrderItems());
        $this->assertEquals(500, $order->getSubtotal());
        $this->assertEquals(-10, $order->getDiscount());
        $this->assertEquals(142, $order->getTax());
        $this->assertEquals(632, $order->getTotal());
    }
}
