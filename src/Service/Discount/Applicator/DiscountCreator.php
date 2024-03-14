<?php

namespace App\Service\Discount\Applicator;

use App\Entity\Order;
use App\Service\Discount\Interface\DiscountInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class DiscountCreator
{
    public function __construct(
        #[TaggedIterator('app.discount.provider')] private readonly iterable $providers,
    ) {
    }

    public function create(Order $order): void
    {
        // set default
        foreach ($order->getOrderItems() as $orderItem) {
            $orderItem->setDiscountGlobal(0);
            $orderItem->setDiscountItem(0);
        }

        foreach ($this->providers as $provider) {
            /** @var DiscountInterface $provider */
            if ($provider->match($order)) {
                $provider->apply($order);
            }
        }
    }
}
