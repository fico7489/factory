<?php

namespace App\Service\Tax\Applicator;

use App\Entity\Order;
use App\Service\Tax\Interface\TaxInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class TaxApplicator
{
    public function __construct(
        #[TaggedIterator('app.tax.provider')] private readonly iterable $providers,
        private readonly EntityManagerInterface $entityManager,
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
