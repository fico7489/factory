<?php

namespace App\Service\Discount\Applicator;

use App\Entity\Order;
use App\Service\Discount\Interface\DiscountInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class Applicator
{
    public function __construct(
        #[TaggedIterator('app.discount.provider')] private readonly iterable $providers,
    ) {
    }

    public function apply(Order $order): void
    {
        foreach ($this->providers as $provider) {
            /** @var DiscountInterface $provider */
            if ($provider->match($order)) {
                $provider->apply($order);
            }
        }
    }
}
