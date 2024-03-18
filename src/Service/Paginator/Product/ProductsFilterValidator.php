<?php

namespace App\Service\Paginator\Product;

use App\Service\Paginator\Interface\ValidatorInterface;

class ProductsFilterValidator implements ValidatorInterface
{
    public function validate(array $context): array
    {
        $filterData = $context['filters'] ?? [];

        // TODO validate filters

        return $filterData;
    }
}
