<?php

namespace App\Service;

use App\Entity\Order\Price\OrderItemPrice;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserPricesCalculator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductPriceUserFetcher $productPriceUserFetcher
    ) {
    }

    public function calculate(User $user): array
    {
        $products = $this->entityManager->getRepository(Product::class)->findBy([]);

        /** @var OrderItemPrice[] $prices */
        $prices = [];
        foreach ($products as $product) {
            $price = $this->productPriceUserFetcher->fetch($user, $product);

            $prices[$product->getId()] = $price->getPrice();
        }

        sort($prices, SORT_NUMERIC);

        return $prices;
    }
}
