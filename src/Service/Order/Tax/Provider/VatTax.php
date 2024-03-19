<?php

namespace App\Service\Order\Tax\Provider;

use App\Entity\Order;
use App\Service\Order\Tax\Interface\TaxInterface;

class VatTax implements TaxInterface
{
    public function name(): string
    {
        return 'vat';
    }

    public function match(Order $order): bool
    {
        return true;
    }

    public function apply(Order $order): void
    {
        foreach ($order->getOrderItems() as $orderItem) {
            $tax = round($orderItem->getTaxBase() * 0.25, 2);

            $orderItem->setTax($orderItem->getTax() + $tax);
        }
    }
}
