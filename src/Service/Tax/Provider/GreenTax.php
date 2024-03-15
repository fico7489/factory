<?php

namespace App\Service\Tax\Provider;

use App\Entity\Category;
use App\Entity\Order;
use App\Service\Tax\Interface\TaxInterface;
use Doctrine\ORM\EntityManagerInterface;

class GreenTax implements TaxInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function name(): string
    {
        return 'green';
    }

    public function match(Order $order): bool
    {
        $total = 0;

        $categoryCharger = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Charger']);

        foreach ($order->getOrderItems() as $orderItem) {
            if ($orderItem->getProduct()->getCategories()->contains($categoryCharger)) {
                return true;
            }
        }

        return false;
    }

    public function apply(Order $order): void
    {
        $categoryCharger = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Charger']);

        foreach ($order->getOrderItems() as $orderItem) {
            if ($orderItem->getProduct()->getCategories()->contains($categoryCharger)) {
                $taxBase = $orderItem->getSubtotal() + $orderItem->getDiscount();

                $tax = $taxBase * 0.1;
                $orderItem->setTax($orderItem->getTax() + $tax);
            }
        }
    }
}
