<?php

namespace App\Service\Order;

use App\Entity\Order;
use App\Service\ProductPriceUserFetcher;
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
            if ($orderItem->getPriceItem()) {
                // clear old one if exists
                $this->entityManager->remove($orderItem->getPriceItem());
                $this->entityManager->flush();

                $this->entityManager->refresh($orderItem);
            }

            $productPriceUser = $this->productPriceUserFetcher->fetch($orderItem->getOrder()->getUser(), $orderItem->getProduct());
            $productPriceUser->setOrderItem($orderItem);
            $this->entityManager->persist($productPriceUser);
            $this->entityManager->flush();

            $orderItem->setPriceItem($productPriceUser);
            $orderItem->setPriceAdjusted($productPriceUser->getPrice());
            $orderItem->setSubtotal($orderItem->getPriceAdjusted() * $orderItem->getQuantity());

            $this->entityManager->persist($orderItem);
            $this->entityManager->flush();
        }

        return $order;
    }
}
