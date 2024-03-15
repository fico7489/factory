<?php

namespace App\Service;

use App\Entity\Order\Price\OrderItemPrice;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ProductPriceUserFetcher
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    // gimme price of product for given user
    public function fetch(User $user, Product $product): OrderItemPrice
    {
        $sku = $product->getSku();

        // add default product price
        $orderItemPrices = [
            new OrderItemPrice(price: $product->getPrice(), type: OrderItemPrice::TYPE_PRODUCT),
        ];

        // add price by contract_list
        $contractList = $this->entityManager->getRepository(Product\ProductContractList::class)->findOneBy(['user' => $user, 'sku' => $sku]);
        if ($contractList) {
            $orderItemPrices[] = new OrderItemPrice(price: $contractList->getPrice(), type: OrderItemPrice::TYPE_CONTRACT_LIST, contractList: $contractList);
        }

        // add prices by price_list and user_groups
        $priceLists = $this->entityManager->getRepository(Product\ProductPriceList::class)->findBy(['userGroup' => $user->getUserGroups()->toArray(), 'sku' => $sku]);
        foreach ($priceLists as $priceList) {
            $orderItemPrices[] = new OrderItemPrice(price: $priceList->getPrice(), type: OrderItemPrice::TYPE_PRICE_LIST, priceList: $priceList);
        }

        usort($orderItemPrices, function ($a, $b) {
            /* @var OrderItemPrice $a */
            /* @var OrderItemPrice $b */

            return $a->getPrice() > $b->getPrice() ? 1 : -1;
        });

        // return most optimal price for the customer
        return $orderItemPrices[0];
    }
}
