<?php

namespace App\Service\Order\Discount\Applicator;

use App\Entity\Order;
use App\Service\Order\Discount\Interface\DiscountInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class DiscountApplicator
{
    public function __construct(
        #[TaggedIterator('app.discount.provider')] private readonly iterable $providers,
    ) {
    }

    public function apply(Order $order): void
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
