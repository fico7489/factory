<?php

namespace App\Tests\Service;

use App\Entity\Order\OrderItem;
use App\Entity\Order\Price\PriceItem;
use App\Service\OrderPlacer;
use App\Tests\TestCase;

class OrderPlacerTest extends TestCase
{
    private OrderPlacer $orderPlacer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderPlacer = $this->container->get(OrderPlacer::class);
    }

    public function testService(): void
    {
        $userGroup = $this->dataProvider->createUserGroup();
        $user = $this->dataProvider->createUser();
        $product = $this->dataProvider->createProduct(40, 'test');

        // test regular order without any adjustment
        $order = $this->orderPlacer->placeOrder($this->dataProvider->getOrderData([1 => 2]));
        /** @var OrderItem $orderItem */
        $orderItem = $order->getOrderItems()[0];
        $this->assertEquals(2, $orderItem->getQuantity());
        $this->assertEquals(40, $orderItem->getPrice());
        $this->assertEquals(40, $orderItem->getPriceAdjusted());
        $this->assertEquals(PriceItem::TYPE_PRODUCT, $orderItem->getProductPriceUser()->getType());
        $this->assertEquals(80, $orderItem->getSubtotal());
        $this->assertEquals(0, $orderItem->getDiscountGlobal());
        $this->assertEquals(0, $orderItem->getDiscountItem());
        $this->assertEquals(0, $orderItem->getDiscount());
        $this->assertEquals(0, $orderItem->getTax());
        $this->assertEquals(80, $orderItem->getTotal());

        //  contract_list
        $this->dataProvider->createContractList($user, 'test', 30);
        $order = $this->orderPlacer->placeOrder($this->dataProvider->getOrderData([1 => 2]));
        /** @var OrderItem $orderItem */
        $orderItem = $order->getOrderItems()[0];
        $this->assertEquals(2, $orderItem->getQuantity());
        $this->assertEquals(40, $orderItem->getPrice());
        $this->assertEquals(30, $orderItem->getPriceAdjusted());
        $this->assertEquals(PriceItem::TYPE_CONTRACT_LIST, $orderItem->getProductPriceUser()->getType());
        $this->assertEquals(60, $orderItem->getSubtotal());
        $this->assertEquals(0, $orderItem->getDiscountGlobal());
        $this->assertEquals(0, $orderItem->getDiscountItem());
        $this->assertEquals(0, $orderItem->getDiscount());
        $this->assertEquals(0, $orderItem->getTax());
        $this->assertEquals(60, $orderItem->getTotal());

        // price_list
        $this->dataProvider->attachUserGroupToUser($userGroup, $user);
        $this->dataProvider->createPriceList($userGroup, 'test', 25);
        $order = $this->orderPlacer->placeOrder($this->dataProvider->getOrderData([1 => 2]));
        /** @var OrderItem $orderItem */
        $orderItem = $order->getOrderItems()[0];
        $this->assertEquals(2, $orderItem->getQuantity());
        $this->assertEquals(40, $orderItem->getPrice());
        $this->assertEquals(25, $orderItem->getPriceAdjusted());
        $this->assertEquals(PriceItem::TYPE_PRICE_LIST, $orderItem->getProductPriceUser()->getType());
        $this->assertEquals(50, $orderItem->getSubtotal());
        $this->assertEquals(0, $orderItem->getDiscountGlobal());
        $this->assertEquals(0, $orderItem->getDiscountItem());
        $this->assertEquals(0, $orderItem->getDiscount());
        $this->assertEquals(0, $orderItem->getTax());
        $this->assertEquals(50, $orderItem->getTotal());
    }
}
