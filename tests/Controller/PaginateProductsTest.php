<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Tests\TestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;

class PaginateProductsTest extends TestCase
{
    public function testService(): void
    {
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

        for ($i = 1; $i < 26; ++$i) {
            $product = new Product();
            $product->setName('test_'.Uuid::uuid4());
            $product->setDescription('decription_'.Uuid::uuid4());
            $product->setPrice((float) (random_int(1, 1000).'.'.random_int(1, 99)));
            $product->setSku(Uuid::uuid4());
            $product->setPublished(true);

            if (1 === $i) {
                $product->setCategories(new ArrayCollection([$categoryOne]));
            } elseif (2 === $i) {
                $product->setCategories(new ArrayCollection([$categoryTwo]));
            } elseif (3 === $i) {
                $product->setCategories(new ArrayCollection([$categoryOneOne]));
            } elseif (4 === $i) {
                $product->setCategories(new ArrayCollection([$categoryOne, $categoryTwo]));
            }

            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();

        $response = $this->client->request('GET', '/api/products', ['headers' => ['Accept' => 'application/vnd.api+json']]);

        $this->assertEquals(10, count($response->toArray()['data']));
        $this->assertEquals(25, $response->toArray()['meta']['totalItems']);

        $response = $this->client->request('GET', '/api/category/1/products/', ['headers' => ['Accept' => 'application/vnd.api+json']]);
        $this->assertEquals(2, count($response->toArray()['data']));

        $response = $this->client->request('GET', '/api/category/2/products/', ['headers' => ['Accept' => 'application/vnd.api+json']]);
        $this->assertEquals(1, count($response->toArray()['data']));

        $response = $this->client->request('GET', '/api/category/3/products/', ['headers' => ['Accept' => 'application/vnd.api+json']]);
        $this->assertEquals(2, count($response->toArray()['data']));
    }
}
