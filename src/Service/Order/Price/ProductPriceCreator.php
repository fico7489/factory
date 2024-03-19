<?php

namespace App\Service\Order\Price;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class ProductPriceCreator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductPriceUserFetcher $productPriceUserFetcher,
    ) {
    }

    // create a fresh order (like cart)
    public function create(Order $order): Order
    {
        foreach ($order->getOrderItems() as $orderItem) {
            if ($orderItem->getOrderPriceItem()) {
                // clear old one if exists
                $this->entityManager->remove($orderItem->getOrderPriceItem());
                $this->entityManager->flush();

                $this->entityManager->refresh($orderItem);
            }

            $productPriceUser = $this->productPriceUserFetcher->fetch($orderItem->getOrder()->getUser(), $orderItem->getProduct());
            $productPriceUser->setOrderItem($orderItem);
            $this->entityManager->persist($productPriceUser);
            $this->entityManager->flush();

            $orderItem->setOrderPriceItem($productPriceUser);
            $orderItem->setPriceAdjusted($productPriceUser->getPrice());
            $orderItem->setSubtotal($orderItem->getPriceAdjusted() * $orderItem->getQuantity());

            $this->entityManager->persist($orderItem);
            $this->entityManager->flush();
        }

        return $order;
    }
}
