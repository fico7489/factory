<?php

namespace App\tests\Service\OrderPlacer\Price;

use App\Entity\Order\OrderItem;
use App\Entity\Order\Price\OrderItemPrice;
use App\Tests\Service\OrderPlacer\TestCaseOrderPlacer;

class ContractPriceTest extends TestCaseOrderPlacer
{
    public function testService(): void
    {
        $userGroup = $this->dataProvider->createUserGroup();
        $user = $this->dataProvider->createUser($userGroup);
        $product = $this->dataProvider->createProduct(19, 'test');

        $priceList = $this->dataProvider->createPriceList($userGroup, 'test', 18);

        $order = $this->orderPlacer->placeOrder($user, [$product->getId() => 1]);

        /** @var OrderItem $orderItem */
        $orderItem = $order->getOrderItems()[0];

        $this->assertEquals(1, $orderItem->getQuantity());
        $this->assertEquals(19, $orderItem->getPrice());
        $this->assertEquals(18, $orderItem->getPriceAdjusted());
        $this->assertEquals(18, $orderItem->getSubtotal());
        $this->assertEquals(0, $orderItem->getDiscountGlobal());
        $this->assertEquals(0, $orderItem->getDiscountItem());
        $this->assertEquals(0, $orderItem->getDiscount());
        $this->assertEquals(4.5, $orderItem->getTax());
        $this->assertEquals(22.5, $orderItem->getTotal());
        $this->assertEquals(OrderItemPrice::TYPE_PRICE_LIST, $orderItem->getPriceItem()->getType());
        $this->assertEquals($priceList, $orderItem->getPriceItem()->getPriceList());

        $this->assertCount(1, $order->getOrderItems());
        $this->assertEquals(18, $order->getSubtotal());
        $this->assertEquals(0, $order->getDiscount());
        $this->assertEquals(4.5, $order->getTax());
        $this->assertEquals(22.5, $order->getTotal());
    }
}
