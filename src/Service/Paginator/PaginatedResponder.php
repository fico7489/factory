<?php

namespace App\Service\Paginator;

use ApiPlatform\State\Pagination\TraversablePaginator;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginatedResponder
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function fetch(array $entities): TraversablePaginator
    {
        $itemsPerPage = $this->parameterBag->get('api_platform.collection.pagination.items_per_page');
        $itemsPerPageParameterName = $this->parameterBag->get('api_platform.collection.pagination.items_per_page_parameter_name');
        $pageParameterName = $this->parameterBag->get('api_platform.collection.pagination.page_parameter_name');

        $currentPage = (int) $this->requestStack->getCurrentRequest()->query->get($pageParameterName, 1);
        $itemsPerPage = (int) $this->requestStack->getCurrentRequest()->query->get($itemsPerPageParameterName, $itemsPerPage);

        return new TraversablePaginator(new \ArrayIterator($entities), $currentPage, $itemsPerPage, count($entities));
    }
}
