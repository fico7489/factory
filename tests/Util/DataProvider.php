<?php

namespace App\Tests\Util;

use App\Entity\Category;
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

    public function createUser(?UserGroup $userGroup = null, bool $flush = true): User
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('secret');

        if ($userGroup) {
            $user->setUserGroups(new ArrayCollection([$userGroup]));
        }

        $this->entityManager->persist($user);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $user;
    }

    public function createUserGroup(string $name = 'user_group_test', bool $flush = true): UserGroup
    {
        $userGroup = new UserGroup();
        $userGroup->setName($name);
        $this->entityManager->persist($userGroup);
        if ($flush) {
            $this->entityManager->flush();
        }

        return $userGroup;
    }

    public function createCategory($name = 'Test'): Category
    {
        $category = new Category();
        $category->setName($name);
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

    public function createProduct(float $price, string $sku = 'test', ?Category $category = null, bool $flush = true): Product
    {
        $product = new Product();
        $product->setName('test');
        $product->setPrice($price);
        $product->setSku($sku);
        $product->setPublished(true);

        if ($category) {
            $product->setCategories(new ArrayCollection([$category]));
        }

        $this->entityManager->persist($product);
        if ($flush) {
            $this->entityManager->flush();
        }

        return $product;
    }

    public function createContractList(User $user, string $sku, float $price, bool $flush = true): Product\ProductContractList
    {
        $contractList = new Product\ProductContractList();
        $contractList->setSku($sku);
        $contractList->setPrice($price);
        $contractList->setUser($user);

        $this->entityManager->persist($contractList);
        if ($flush) {
            $this->entityManager->flush();
        }

        return $contractList;
    }

    public function createPriceList(UserGroup $userGroup, string $sku, float $price, bool $flush = true): Product\ProductPriceList
    {
        $priceList = new Product\ProductPriceList();
        $priceList->setPrice($price);
        $priceList->setSku($sku);
        $priceList->setUserGroup($userGroup);
        $this->entityManager->persist($priceList);
        if ($flush) {
            $this->entityManager->flush();
        }

        return $priceList;
    }
}
