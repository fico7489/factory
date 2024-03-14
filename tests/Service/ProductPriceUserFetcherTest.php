<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserGroup;
use App\Service\ProductPriceUserFetcher;
use App\Tests\TestCase;
use Doctrine\Common\Collections\ArrayCollection;

class ProductPriceUserFetcherTest extends TestCase
{
    public function testService(): void
    {
        /** @var ProductPriceUserFetcher $productPriceCalculator */
        $productPriceCalculator = $this->container->get(ProductPriceUserFetcher::class);

        $product = new Product();
        $product->setName('test');
        $product->setPrice(10);
        $product->setSku('test');
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $userGroup = new UserGroup();
        $userGroup->setName('user_group_test');
        $this->entityManager->persist($userGroup);
        $this->entityManager->flush();

        $user = new User();
        $user->setUserGroups(new ArrayCollection([$userGroup]));
        $user->setEmail('test@example.com');
        $user->setPassword('secret');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // detect by product
        $productPrice = $productPriceCalculator->fetch($user, $product);
        $this->assertEquals(10, $productPrice->getPrice());
        $this->assertEquals('product', $productPrice->getType());
        $this->assertEquals(null, $productPrice->getPriceList());
        $this->assertEquals(null, $productPrice->getContractList());

        // detect by contract_list
        $contractList = new Product\ContractList();
        $contractList->setSku('test');
        $contractList->setPrice(9);
        $contractList->setUser($user);
        $this->entityManager->persist($contractList);
        $this->entityManager->flush();

        $productPrice = $productPriceCalculator->fetch($user, $product);
        $this->assertEquals(9, $productPrice->getPrice());
        $this->assertEquals('contract_list', $productPrice->getType());
        $this->assertEquals($contractList, $productPrice->getContractList());

        // detect by price_list
        $priceList = new Product\PriceList();
        $priceList->setPrice(8);
        $priceList->setSku('test');
        $priceList->setUserGroup($userGroup);
        $this->entityManager->persist($priceList);
        $this->entityManager->flush();

        $productPrice = $productPriceCalculator->fetch($user, $product);
        $this->assertEquals(8, $productPrice->getPrice());
        $this->assertEquals('price_list', $productPrice->getType());
        $this->assertEquals($priceList, $productPrice->getPriceList());
    }
}
