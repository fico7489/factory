<?php

namespace App\DataProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\ProductRepository;

final class ProductsDataProvider implements ProviderInterface
{
    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $filterData = $context['filters'];

        $filters = $filterData['filters'] ?? [];
        $sorts = $filterData['sorts'] ?? [];

        // TODO paginator

        $products = $this->productRepository->findByFilters($filters, $sorts);

        return $products;
    }
}
