<?php

namespace App\Tests\Util;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class DataProvider
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getOrderData(array $productIds): array
    {
        $data = [
            'user_id' => 1,
            'items' => [],
        ];

        foreach ($productIds as $productId => $quantity) {
            $data['items'][] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }

        return $data;
    }

    public function createUser(): User
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('secret');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function createUserGroup(string $name = 'user_group_test'): UserGroup
    {
        $userGroup = new UserGroup();
        $userGroup->setName($name);
        $this->entityManager->persist($userGroup);
        $this->entityManager->flush();

        return $userGroup;
    }

    public function attachUserGroupToUser(UserGroup $userGroup, User $user): User
    {
        $user->setUserGroups(new ArrayCollection([$userGroup]));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function createProduct(float $price, string $sku = 'test'): Product
    {
        $product = new Product();
        $product->setName('test');
        $product->setPrice($price);
        $product->setSku($sku);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    public function createContractList(User $user, string $sku, float $price): Product\ContractList
    {
        $contractList = new Product\ContractList();
        $contractList->setSku($sku);
        $contractList->setPrice($price);
        $contractList->setUser($user);

        $this->entityManager->persist($contractList);
        $this->entityManager->flush();

        return $contractList;
    }

    public function createPriceList(UserGroup $userGroup, string $sku, float $price): Product\PriceList
    {
        $priceList = new Product\PriceList();
        $priceList->setPrice($price);
        $priceList->setSku($sku);
        $priceList->setUserGroup($userGroup);
        $this->entityManager->persist($priceList);
        $this->entityManager->flush();

        return $priceList;
    }
}
