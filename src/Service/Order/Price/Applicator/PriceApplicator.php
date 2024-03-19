<?php

namespace App\Service\Order\Price\Applicator;

use App\Entity\Order;
use App\Service\Order\Price\Fetcher\OrderItemPriceFetcher;
use Doctrine\ORM\EntityManagerInterface;

class PriceApplicator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderItemPriceFetcher $productPriceUserFetcher,
    ) {
    }

    public function apply(Order $order): Order
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
