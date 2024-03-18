<?php

namespace App\Tests\Controller\Product;

use App\Entity\Category;
use App\Entity\Product;
use App\Tests\TestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;

class PaginateV2ProductsTest extends TestCase
{
    public function testService(): void
    {
        $this->asUserId(1);

        list($userFirst, $userSecond, $userThird, $productFirst, $productSecond) = $this->prepareProducts();

        //test matching price_adjusted, 100 for third user
        $this->asUser($userThird);
        $response = $this->client->request('GET', '/api/v2/products?sorts[price_adjusted]=asc');
        $firstProduct = $response->toArray()['data'][0]['attributes'];
        $this->assertEquals(100, $firstProduct['priceAdjusted']);

        //test matching price_adjusted, 100 for second user
        $this->asUser($userSecond);
        $response = $this->client->request('GET', '/api/v2/products?sorts[price_adjusted]=asc');
        $firstProduct = $response->toArray()['data'][0]['attributes'];
        $this->assertEquals(99, $firstProduct['priceAdjusted']);

        //test matching price_adjusted, 100 for first user
        $this->asUser($userFirst);
        $response = $this->client->request('GET', '/api/v2/products?sorts[price_adjusted]=asc');
        $firstProduct = $response->toArray()['data'][0]['attributes'];
        $this->assertEquals(98, $firstProduct['priceAdjusted']);

        //test sorting by price -> asc
        $this->asUser($userFirst);
        $response = $this->client->request('GET', '/api/v2/products?sorts[price_adjusted]=asc');
        $firstProduct = $response->toArray()['data'][0]['attributes'];
        $this->assertEquals(98, $firstProduct['priceAdjusted']);
        $this->assertEquals($productFirst->getId(), $firstProduct['_id']);

        //test sorting by price -> desc
        $this->asUser($userFirst);
        $response = $this->client->request('GET', '/api/v2/products?sorts[price_adjusted]=desc');
        $firstProduct = $response->toArray()['data'][0]['attributes'];
        $this->assertEquals(102, $firstProduct['priceAdjusted']);
        $this->assertEquals($productSecond->getId(), $firstProduct['_id']);
    }

    private function prepareProducts(): array
    {
        $userGroupFirst = $this->dataProvider->createUserGroup('First');
        $userGroupSecond = $this->dataProvider->createUserGroup('Second');

        $userFirst = $this->dataProvider->createUser($userGroupFirst);
        $userSecond = $this->dataProvider->createUser($userGroupSecond);
        $userThird = $this->dataProvider->createUser();

        $categoryOne = new Category();
        $categoryOne->setName('One');

        $categoryOneOne = new Category();
        $categoryOneOne->setName('OneOne');
        $categoryOneOne->setParent($categoryOne);

        $categoryTwo = new Category();
        $categoryTwo->setName('Two');

        $this->entityManager->persist($categoryOne);
        $this->entityManager->persist($categoryOneOne);
        $this->entityManager->persist($categoryTwo);

        $this->entityManager->flush();

        $productFirst = null;
        $productSecond = null;
        for ($i = 1; $i < 26; ++$i) {
            $sku = Uuid::uuid4();

            $product = new Product();
            $product->setName('test_'.Uuid::uuid4());
            $product->setDescription('decription_'.Uuid::uuid4());
            $product->setPrice(101);
            $product->setSku($sku);
            $product->setPublished(true);

            if (1 === $i) {
                $product->setCategories(new ArrayCollection([$categoryOne]));
                $product->setPrice(100);

                $this->dataProvider->createPriceList($userGroupSecond, $sku, 99);

                $this->dataProvider->createContractList($userFirst, $sku, 98);

                $productFirst = $product;
            } elseif (2 === $i) {
                $product->setPrice(102);
                $product->setCategories(new ArrayCollection([$categoryTwo]));

                $productSecond = $product;
            } elseif (3 === $i) {
                $product->setCategories(new ArrayCollection([$categoryOneOne]));
            } elseif (4 === $i) {
                $product->setCategories(new ArrayCollection([$categoryOne, $categoryTwo]));
            }

            $product->setCategories(new ArrayCollection([$categoryOne]));

            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();

        return [$userFirst, $userSecond, $userThird, $productFirst, $productSecond];
    }
}
