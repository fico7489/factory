<?php

namespace App\Service\Paginator\Interface;

interface DataProviderInterface
{
    public function paginate(array $filterData, int $limit, int $offset): array;
}
