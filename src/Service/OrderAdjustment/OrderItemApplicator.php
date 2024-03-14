<?php

namespace App\Service\OrderAdjustment;

use App\Entity\Order\OrderItem;
use App\Service\ProductPriceUserFetcher;
use Doctrine\ORM\EntityManagerInterface;

class OrderItemApplicator
{
    public function __construct(
        private readonly ProductPriceUserFetcher $productPriceUserFetcher,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function apply(OrderItem $orderItem): OrderItem
    {
        $this->applyPriceAdjustments($orderItem);
        $this->applyDiscount($orderItem);
        $this->applyTaxAfterDiscount($orderItem);
        $this->applyQuantity($orderItem);

        return $orderItem;
    }

    private function applyPriceAdjustments(OrderItem $orderItem): void
    {
        $productPriceUser = $this->productPriceUserFetcher->fetch($orderItem->getOrder()->getUser(), $orderItem->getProduct());

        $orderItem->setProductPriceUser($productPriceUser);

        $orderItem->setPriceAdjusted($productPriceUser->getPrice());
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
