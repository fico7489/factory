<?php

namespace App\Service\Paginator\Product;

use App\Entity\User;

class FilterValidator
{
    public function validate(array $context): array
    {
        $filterData = $context['filters'] ?? [];

        //TODO validate filters

        return $filterData;
    }
}
