<?php

namespace App\Service;

use App\Entity\Order;
use App\Service\Discount\Applicator\Applicator;
use Doctrine\ORM\EntityManagerInterface;

class OrderApplicator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductPriceUserFetcher $productPriceUserFetcher,
        private readonly Applicator $applicator,
    ) {
    }

    public function apply(Order $order): Order
    {
        // apply price adjustments
        $this->applyPriceAdjustments($order);  // TODO service

        // apply discounts
        $this->applicator->apply($order);

        // apply taxes
        $this->applyTaxes($order);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    private function applyPriceAdjustments(Order $order): void
    {
        foreach ($order->getOrderItems() as $orderItem) {
            $productPriceUser = $this->productPriceUserFetcher->fetch($orderItem->getOrder()->getUser(), $orderItem->getProduct());
            $productPriceUser->setOrderItem($orderItem);
            $this->entityManager->persist($productPriceUser);
            $this->entityManager->flush();

            $orderItem->setProductPriceUser($productPriceUser);

            $orderItem->setPriceAdjusted($productPriceUser->getPrice());

            $this->entityManager->persist($orderItem);
            $this->entityManager->flush();
        }
    }

    private function applyTaxes(Order $order): void
    {
        // TODO
    }
}
