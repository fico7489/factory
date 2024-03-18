<?php

namespace App\DataProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Service\Paginator\Interface\DataProviderInterface;
use App\Service\Paginator\Interface\ValidatorInterface;
use App\Service\Paginator\Paginator;
use App\Service\Paginator\Product\ProductsDataProviderSql;
use App\Service\Paginator\Product\ProductsFilterValidator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

// data provider for products paginator, we can change implementation from mysql to ES with configuration, tests are covering everything in each implementation
final class ProductsDataProvider implements ProviderInterface
{
    public function __construct(
        private readonly Paginator $paginator,
        #[Autowire(service: ProductsDataProviderSql::class)] private readonly DataProviderInterface $dataProvider,
        #[Autowire(service: ProductsFilterValidator::class)] private readonly ValidatorInterface $validator,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->paginator->paginate($context, $this->dataProvider, $this->validator);
    }
}
