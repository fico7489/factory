<?php

namespace App\Tests\Service\OrderPlacer;

use App\Service\Order\OrderPlacer;
use App\Tests\TestCase;

class TestCaseOrderPlacer extends TestCase
{
    protected OrderPlacer $orderPlacer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderPlacer = $this->container->get(OrderPlacer::class);
    }
}
