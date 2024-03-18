<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Service\Discount\Applicator\DiscountApplicator;
use App\Service\Order\OrderCreator;
use App\Service\Order\OrderItemCreator;
use App\Service\Order\ProductPriceCreator;
use App\Service\Tax\Applicator\TaxApplicator;
use Doctrine\ORM\EntityManagerInterface;

class OrderPlacer
{
    public function __construct(
        private readonly OrderCreator $orderCreator,
        private readonly OrderItemCreator $orderItemCreator,
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductPriceCreator $productPriceCreator,
        private readonly DiscountApplicator $applicator,
        private readonly TaxApplicator $taxApplicator,
    ) {
    }

    public function placeOrder(User $user, array $items): Order
    {
        // create order
        $order = $this->orderCreator->create($user);

        // create order items
        foreach ($items as $productId => $quantity) {
            $product = $this->entityManager->getRepository(Product::class)->find($productId);

            $this->orderItemCreator->create($order, $product, $quantity);
        }
        $this->entityManager->refresh($order);

        // apply price adjustments
        $this->productPriceCreator->create($order);

        // apply discounts
        $this->applicator->apply($order);

        // apply taxes
        $this->taxApplicator->apply($order);

        // calculate total
        foreach ($order->getOrderItems() as $orderItem) {
            $total = $orderItem->getSubtotal() + $orderItem->getDiscount() + $orderItem->getTax();

            $orderItem->setTotal($total);
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }
}
