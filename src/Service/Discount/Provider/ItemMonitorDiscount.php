<?php

namespace App\Service\Discount\Provider;

use App\Entity\Category;
use App\Entity\Order;
use App\Service\Discount\Interface\DiscountInterface;
use Doctrine\ORM\EntityManagerInterface;

class ItemMonitorDiscount implements DiscountInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function name(): string
    {
        return 'item_monitor';
    }

    public function match(Order $order): bool
    {
        $total = 0;

        $categoryMonitor = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Monitor']);

        foreach ($order->getOrderItems() as $orderItem) {
            if ($orderItem->getProduct()->getCategories()->contains($categoryMonitor)) {
                return true;
            }
        }

        return false;
    }

    public function apply(Order $order): void
    {
        $orderDiscount = new Order\Discount\DiscountItem();
        $orderDiscount->setName(Global100Discount::class);

        $categoryMonitor = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Monitor']);
        foreach ($order->getOrderItems() as $orderItem) {
            if ($orderItem->getProduct()->getCategories()->contains($categoryMonitor)) {
                $discountItemAmount = -($orderItem->getSubtotal() * 0.1);

                $orderItem->setDiscountItem($orderItem->getDiscountItem() + $discountItemAmount);
            }
        }
    }
}
