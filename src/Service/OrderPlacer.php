<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Service\Discount\Applicator\DiscountCreator;
use App\Service\Order\OrderCreator;
use App\Service\Order\OrderItemCreator;
use App\Service\Order\ProductPriceCreator;
use Doctrine\ORM\EntityManagerInterface;

class OrderPlacer
{
    public function __construct(
        private readonly OrderCreator $orderCreator,
        private readonly OrderItemCreator $orderItemCreator,
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductPriceCreator $productPriceCreator,
        private readonly DiscountCreator $applicator,
    ) {
    }

    public function placeOrder(array $data)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $data['user_id']]);

        // create order
        $order = $this->orderCreator->create($user);

        // create order items
        foreach ($data['items'] as $item) {
            $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $item['product_id']]);

            $this->orderItemCreator->create($order, $product, $item['quantity']);
        }
        $this->entityManager->refresh($order);

        // apply price adjustments
        $this->productPriceCreator->create($order);

        // apply discounts
        $this->applicator->create($order);

        // apply taxes
        $this->applyTaxes($order);

        // calculate total
        foreach ($order->getOrderItems() as $orderItem) {
            $total = $orderItem->getSubtotal() + $orderItem->getDiscount() + $orderItem->getTax();

            $orderItem->setTotal($total);
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    private function applyTaxes(Order $order): void
    {
        // TODO -> and to service
    }
}
