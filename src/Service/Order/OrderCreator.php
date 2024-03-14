<?php

namespace App\Service\Order;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class OrderCreator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    // create a fresh order (like cart)
    public function create(User $user): Order
    {
        $order = new Order();

        $order->setUser($user);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }
}
