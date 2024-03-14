<?php

namespace App\Service;

use App\Entity\Order\ProductPriceUser;
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
    public function fetch(User $user, Product $product): ProductPriceUser
    {
        $sku = $product->getSku();

        // add default product price
        $orderItemPrices = [
            new ProductPriceUser(price: $product->getPrice(), type: ProductPriceUser::TYPE_PRODUCT),
        ];

        // add price by contract_list
        $contractList = $this->entityManager->getRepository(Product\ContractList::class)->findOneBy(['user' => $user, 'sku' => $sku]);
        if ($contractList) {
            $orderItemPrices[] = new ProductPriceUser(price: $contractList->getPrice(), type: ProductPriceUser::TYPE_CONTRACT_LIST, contractList: $contractList);
        }

        // add prices by price_list and user_groups
        $priceLists = $this->entityManager->getRepository(Product\PriceList::class)->findBy(['userGroup' => $user->getUserGroups()->toArray(), 'sku' => $sku]);
        foreach ($priceLists as $priceList) {
            $orderItemPrices[] = new ProductPriceUser(price: $priceList->getPrice(), type: ProductPriceUser::TYPE_PRICE_LIST, priceList: $priceList);
        }

        usort($orderItemPrices, function ($a, $b) {
            /* @var ProductPriceUser $a */
            /* @var ProductPriceUser $b */

            return $a->getPrice() > $b->getPrice() ? 1 : -1;
        });

        // return most optimal price for the customer
        return $orderItemPrices[0];
    }
}
