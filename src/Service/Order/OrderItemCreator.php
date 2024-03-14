<?php

namespace App\Service\Order;

use App\Entity\Order;
use App\Entity\Order\OrderItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class OrderItemCreator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    // add order_item to order
    public function create(Order $order, Product $product, int $quantity): OrderItem
    {
        $orderItem = new OrderItem();

        $orderItem->setQuantity($quantity);
        $orderItem->setPrice($product->getPrice());
        $orderItem->setProduct($product);
        $orderItem->setOrder($order);

        $this->entityManager->persist($orderItem);
        $this->entityManager->flush();

        return $orderItem;
    }
}
