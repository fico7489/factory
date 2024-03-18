<?php

namespace App\Service\Paginator\Interface;

// interface for providing entities for paginator, we can use sql, ES or some other implementation
interface DataProviderInterface
{
    public function paginate(array $filterData, int $limit, int $offset): array;
}
