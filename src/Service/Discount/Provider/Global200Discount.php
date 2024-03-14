<?php

namespace App\Service\Discount\Provider;

use App\Entity\Order;
use App\Service\Discount\Interface\DiscountInterface;

class Global200Discount implements DiscountInterface
{
    public function name(): string
    {
        return 'global_200';
    }

    public function match(Order $order): bool
    {
        $total = 0;

        foreach ($order->getOrderItems() as $orderItem) {
            $total += $orderItem->getPriceAdjusted();
        }

        return $total > 200;
    }

    public function apply(Order $order): void
    {
        $orderDiscount = new Order\Discount\DiscountItem();
        $orderDiscount->setName($this->name());

        // TODO
    }
}
