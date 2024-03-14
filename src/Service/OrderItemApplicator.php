<?php

namespace App\Service;

use App\Entity\OrderItem;

class OrderItemApplicator
{
    public function apply(OrderItem $orderItem): OrderItem
    {
        $this->applyPriceAdjustments($orderItem);
        $this->applyTaxPreDiscount($orderItem);
        $this->applyDiscount($orderItem);
        $this->applyTaxAfterDiscount($orderItem);
        $this->applyQuantity($orderItem);

        return $orderItem;
    }

    private function applyPriceAdjustments(OrderItem $orderItem)
    {
        // TODO
    }

    private function applyTaxPreDiscount(OrderItem $orderItem)
    {
        // TODO
    }

    private function applyDiscount(OrderItem $orderItem)
    {
        // TODO
    }

    private function applyTaxAfterDiscount(OrderItem $orderItem)
    {
        // TODO
    }

    private function applyQuantity(OrderItem $orderItem)
    {
        // TODO
    }
}
