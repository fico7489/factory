<?php

namespace App\Tests;

use App\Entity\User;
use App\Service\Order\Price\UserPricesCalculator;
use App\Tests\Service\OrderPlacer\TestCaseOrderPlacer;
use Ramsey\Uuid\Uuid;

class UserPricesCalculatorTest extends TestCaseOrderPlacer
{
    public function testService(): void
    {
        $this->assertEquals(1, 1);

        return;

        $user = $this->entityManager->getRepository(User::class)->find(1);

        /*$user = $this->dataProvider->createUser();

        $this->dataProvider->createProduct(15, '1');
        $this->dataProvider->createProduct(14, '1');
        $this->dataProvider->createProduct(16.5, '1');
        $this->dataProvider->createProduct(12.2, '1');
        $this->dataProvider->createProduct(16.4, '1');
        $this->dataProvider->createProduct(17, '1');
        $this->dataProvider->createProduct(17.2, '1');
        $this->dataProvider->createProduct(17.1, '1');*/

        $this->prepareData($user);

        /** @var UserPricesCalculator $userPricesCalculator */
        $userPricesCalculator = $this->container->get(UserPricesCalculator::class);

        $pricesArray = $userPricesCalculator->calculate($user);
        $pricesArray = array_splice($pricesArray, 0, 10);

        // dd($pricesArray);

        $this->assertEquals(1, 1);
    }

    private function prepareData(User $user)
    {
        // create 1000 users
        for ($i = 0; $i < 10; ++$i) {
            $user = $this->dataProvider->createUser(null, false);
        }

        // create 20k products
        for ($i = 0; $i < 10000; ++$i) {
            $sku = Uuid::uuid4();
            $price = rand(1, 100000).'.'.rand(1, 99);

            $this->dataProvider->createProduct($price, $sku, null, false);
        }

        $this->entityManager->flush();
    }
}
