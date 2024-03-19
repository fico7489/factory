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
        list($userFirst, $userSecond, $userThird, $productFirst, $productSecond, $categoryOneOne) = $this->prepareProducts();

        $this->asUser($userThird);
        $response = $this->request('GET', '/api/v2/products?sorts[][price_adjusted]=asc', [], 'Test matching price_adjusted, 100 for third user');
        $data = $response->toArray()['data'][0]['attributes'];
        $this->assertEquals(100, $data['priceAdjusted']);

        $this->asUser($userSecond);
        $response = $this->request('GET', '/api/v2/products?sorts[][price_adjusted]=asc', [], 'Test matching price_adjusted, 99 for second user');
        $data = $response->toArray()['data'][0]['attributes'];
        $this->assertEquals(99, $data['priceAdjusted']);

        $this->asUser($userFirst);
        $response = $this->request('GET', '/api/v2/products?sorts[][price_adjusted]=asc', [], 'Test matching price_adjusted, 98 for first user');
        $data = $response->toArray()['data'][0]['attributes'];
        $this->assertEquals(98, $data['priceAdjusted']);

        $this->asUser($userFirst);
        $response = $this->request('GET', '/api/v2/products?sorts[][price_adjusted]=asc', [], 'Test sorting by price -> asc');
        $data = $response->toArray()['data'][0]['attributes'];
        $this->assertEquals(98, $data['priceAdjusted']);
        $this->assertEquals($productFirst->getId(), $data['_id']);

        $this->asUser($userFirst);
        $response = $this->request('GET', '/api/v2/products?sorts[][price_adjusted]=desc', [], 'Test sorting by price -> desc');
        $data = $response->toArray()['data'][0]['attributes'];
        $this->assertEquals(102, $data['priceAdjusted']);
        $this->assertEquals($productSecond->getId(), $data['_id']);

        $this->asUser($userFirst);
        $response = $this->request('GET', '/api/v2/products?sorts[][name]=asc', [], 'Test sorting by name -> asc');
        $data = $response->toArray()['data'][0]['attributes'];
        $this->assertEquals($productFirst->getId(), $data['_id']);

        $this->asUser($userFirst);
        $response = $this->request('GET', '/api/v2/products?sorts[][name]=desc', [], 'Test sorting by name -> desc');
        $data = $response->toArray()['data'][0]['attributes'];
        $this->assertEquals($productSecond->getId(), $data['_id']);

        $response = $this->request('GET', '/api/v2/products', [], 'Test filter -> all');
        $this->assertEquals(10, count($response->toArray()['data']));

        $this->asUser($userFirst);
        $response = $this->request('GET', '/api/v2/products?filters[][name][starts_with]=aaaa_Firs', [], 'Test filter -> by name');
        $this->assertEquals(1, count($response->toArray()['data']));

        $this->asUser($userFirst);
        $response = $this->request('GET', '/api/v2/products?filters[][category][equals]='.$categoryOneOne->getId(), [], 'Test filter -> by category');
        $this->assertEquals(2, count($response->toArray()['data']));

        $this->asUser($userFirst);
        $response = $this->request('GET', '/api/v2/products?filters[][price][lte]=99.2', [], 'Test filter -> by price');
        $this->assertEquals(1, count($response->toArray()['data']));
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
            $product->setName('product_'.Uuid::uuid4());
            $product->setDescription('decription_'.Uuid::uuid4());
            $product->setPrice(101);
            $product->setSku($sku);
            $product->setPublished(true);

            if (1 === $i) {
                $product->setCategories(new ArrayCollection([$categoryOne, $categoryOneOne]));
                $product->setPrice(100);
                $product->setName('aaaa_First');

                $this->dataProvider->createPriceList($userGroupSecond, $sku, 99);

                $this->dataProvider->createContractList($userFirst, $sku, 98);

                $productFirst = $product;
            } elseif (2 === $i) {
                $product->setPrice(102);
                $product->setCategories(new ArrayCollection([$categoryTwo]));
                $product->setName('zzzz_First');

                $productSecond = $product;
            } elseif (3 === $i) {
                $product->setCategories(new ArrayCollection([$categoryOneOne]));
            } elseif (4 === $i) {
                $product->setCategories(new ArrayCollection([$categoryOne, $categoryTwo]));
            } else {
                $product->setCategories(new ArrayCollection([$categoryOne]));
            }

            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();

        return [$userFirst, $userSecond, $userThird, $productFirst, $productSecond, $categoryOneOne];
    }
}
