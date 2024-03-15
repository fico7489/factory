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
        return $order->getSubtotal() > 100;
    }

    public function apply(Order $order): void
    {
        $discountAmount = -10;

        $orderDiscount = new Order\Discount\DiscountItem();
        $orderDiscount->setName($this->name());

        $discountGlobalAmount = round($discountAmount / $order->getOrderItems()->count(), 2);

        foreach ($order->getOrderItems() as $orderItem) {
            $orderItem->setDiscountGlobal($orderItem->getDiscountGlobal() + $discountGlobalAmount);
        }
    }
}
