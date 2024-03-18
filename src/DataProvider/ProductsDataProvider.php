<?php

namespace App\DataProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Repository\ProductRepository;
use App\Service\Paginator\PaginatedResponder;
use App\Service\Paginator\Product\FilterValidator;

final class ProductsDataProvider implements ProviderInterface
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly FilterValidator $filterValidator,
        private readonly PaginatedResponder $paginatedResponder,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $filterData = $this->filterValidator->validate($context);
        $products = $this->productRepository->paginateByFilters($filterData);

        return $this->paginatedResponder->fetch($products);
    }
}
