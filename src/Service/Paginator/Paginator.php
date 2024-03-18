<?php

namespace App\Service\Paginator;

use ApiPlatform\State\Pagination\TraversablePaginator;
use App\Service\Paginator\Interface\DataProviderInterface;
use App\Service\Paginator\Interface\ValidatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Paginator
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function paginate(array $context, DataProviderInterface $dataProvider, ValidatorInterface $validator): TraversablePaginator
    {
        $itemsPerPage = $this->parameterBag->get('api_platform.collection.pagination.items_per_page');
        $itemsPerPageParameterName = $this->parameterBag->get('api_platform.collection.pagination.items_per_page_parameter_name');
        $pageParameterName = $this->parameterBag->get('api_platform.collection.pagination.page_parameter_name');

        $currentPage = (int) $this->requestStack->getCurrentRequest()->query->get($pageParameterName, 1);
        $itemsPerPage = (int) $this->requestStack->getCurrentRequest()->query->get($itemsPerPageParameterName, $itemsPerPage);
        $offset = ($currentPage - 1) * $itemsPerPage;

        $filterData = $validator->validate($context);
        $entities = $dataProvider->paginate($filterData, $itemsPerPage, $offset);

        return new TraversablePaginator(new \ArrayIterator($entities), $currentPage, $itemsPerPage, count($entities));
    }
}
