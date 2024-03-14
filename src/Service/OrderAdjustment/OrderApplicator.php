<?php

namespace App\Service\OrderAdjustment;

use App\Entity\Order;
use App\Service\OrderItemApplicator;

class OrderApplicator
{
    public function __construct(
        private readonly OrderItemApplicator $orderItemApplicator,
    ) {
    }

    public function apply(Order $order): Order
    {
        foreach ($order->getOrderItems() as $orderItem) {
            $this->orderItemApplicator->apply($orderItem);
        }

        $this->applyTaxPreDiscount($order);
        $this->applyDiscount($order);
        $this->applyTaxAfterDiscount($order);

        return $order;
    }

    private function applyTaxPreDiscount(Order $order)
    {
        // TODO
    }

    private function applyDiscount(Order $order)
    {
        // TODO
    }

    private function applyTaxAfterDiscount(Order $order)
    {
        // TODO
    }
}
