<?php

namespace App\Service\Tax\Provider;

use App\Entity\Order;
use App\Service\Tax\Interface\TaxInterface;
use Doctrine\ORM\EntityManagerInterface;

class VatTax implements TaxInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

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
            $taxBase = $orderItem->getSubtotal() + $orderItem->getDiscount();

            $tax = $taxBase * 0.25;
            $orderItem->setTax($orderItem->getTax() + $tax);
        }
        // $this->entityManager->refresh($order);
    }
}
