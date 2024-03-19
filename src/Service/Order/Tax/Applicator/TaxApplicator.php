<?php

namespace App\Service\Order\Tax\Applicator;

use App\Entity\Order;
use App\Service\Order\Tax\Interface\TaxInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class TaxApplicator
{
    public function __construct(
        #[TaggedIterator('app.tax.provider')] private readonly iterable $providers,
    ) {
    }

    public function apply(Order $order): void
    {
        // set default
        foreach ($order->getOrderItems() as $orderItem) {
            $orderItem->setTax(0);
        }

        foreach ($this->providers as $provider) {
            /** @var TaxInterface $provider */
            if ($provider->match($order)) {
                $provider->apply($order);
            }
        }
    }
}
