<?php

namespace App\tests\Service\OrderPlacer\Price;

use App\Entity\Order\OrderItem;
use App\Entity\Order\Price\PriceItem;
use App\Tests\Service\OrderPlacer\TestCaseOrderPlacer;

class ContractListTest extends TestCaseOrderPlacer
{
    public function testService(): void
    {
        $user = $this->dataProvider->createUser();
        $this->dataProvider->createProduct(45, 'test');

        $contractList = $this->dataProvider->createContractList($user, 'test', 27);

        $order = $this->orderPlacer->placeOrder($this->dataProvider->getOrderData([1 => 3]));

        /** @var OrderItem $orderItem */
        $orderItem = $order->getOrderItems()[0];

        $this->assertEquals(3, $orderItem->getQuantity());
        $this->assertEquals(45, $orderItem->getPrice());
        $this->assertEquals(27, $orderItem->getPriceAdjusted());
        $this->assertEquals(81, $orderItem->getSubtotal());
        $this->assertEquals(0, $orderItem->getDiscountGlobal());
        $this->assertEquals(0, $orderItem->getDiscountItem());
        $this->assertEquals(0, $orderItem->getDiscount());
        $this->assertEquals(20.25, $orderItem->getTax());
        $this->assertEquals(101.25, $orderItem->getTotal());
        $this->assertEquals(PriceItem::TYPE_CONTRACT_LIST, $orderItem->getPriceItem()->getType());
        $this->assertEquals($contractList, $orderItem->getPriceItem()->getContractList());

        $this->assertCount(1, $order->getOrderItems());
        $this->assertEquals(81, $order->getSubtotal());
        $this->assertEquals(0, $order->getDiscount());
        $this->assertEquals(20.25, $order->getTax());
        $this->assertEquals(101.25, $order->getTotal());
    }
}
