<?php

namespace App\Service\Discount\Provider;

use App\Entity\Order;
use App\Service\Discount\Interface\DiscountInterface;

class Global100Discount implements DiscountInterface
{
    public function name(): string
    {
        return 'global_100';
    }

    public function match(Order $order): bool
    {
        $total = 0;

        foreach ($order->getOrderItems() as $orderItem) {
            $total += $orderItem->getPriceAdjusted();
        }

        return $total > 100 and $total < 200;
    }

    public function apply(Order $order): void
    {
        $discountGlobalAmount = -10;

        $orderDiscount = new Order\Discount\DiscountItem();
        $orderDiscount->setName($this->name());

        $orderDiscountAdjustment = new Order\Discount\DiscountItemAdjustment();
        $orderDiscountAdjustment->setAmount($discountGlobalAmount);

        $discountAmount = $order->getSubtotal() + $discountGlobalAmount;

        $discountAmountItem = $discountAmount / $order->getOrderItems()->count();

        foreach ($order->getOrderItems() as $orderItem) {
            $orderItem->setDiscountGlobal($orderItem->getDiscount() + $discountAmountItem);
        }
    }
}
