<?php

namespace App\Service;

use App\Entity\Order\Price\PriceItem;
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
    public function fetch(User $user, Product $product): PriceItem
    {
        $sku = $product->getSku();

        // add default product price
        $orderItemPrices = [
            new PriceItem(price: $product->getPrice(), type: PriceItem::TYPE_PRODUCT),
        ];

        // add price by contract_list
        $contractList = $this->entityManager->getRepository(Product\ContractList::class)->findOneBy(['user' => $user, 'sku' => $sku]);
        if ($contractList) {
            $orderItemPrices[] = new PriceItem(price: $contractList->getPrice(), type: PriceItem::TYPE_CONTRACT_LIST, contractList: $contractList);
        }

        // add prices by price_list and user_groups
        $priceLists = $this->entityManager->getRepository(Product\PriceList::class)->findBy(['userGroup' => $user->getUserGroups()->toArray(), 'sku' => $sku]);
        foreach ($priceLists as $priceList) {
            $orderItemPrices[] = new PriceItem(price: $priceList->getPrice(), type: PriceItem::TYPE_PRICE_LIST, priceList: $priceList);
        }

        usort($orderItemPrices, function ($a, $b) {
            /* @var PriceItem $a */
            /* @var PriceItem $b */

            return $a->getPrice() > $b->getPrice() ? 1 : -1;
        });

        // return most optimal price for the customer
        return $orderItemPrices[0];
    }
}
