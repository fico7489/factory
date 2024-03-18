<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// filter products by category for that one custom api call
class ProductsPerCategoryExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass, $context);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass, array $context = [])
    {
        if ('_api_category/{category}/products/_get_collection' !== $context['operation_name']) {
            return;
        }

        $categoryId = $context['uri_variables']['category'] ?? null;
        $category = $this->entityManager->getRepository(Category::class)->find($categoryId);

        if (!$category) {
            throw new NotFoundHttpException();
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->leftJoin($rootAlias.'.categories', 'c');
        $queryBuilder->where('c.id in (:categories)');
        $queryBuilder->setParameter('categories', [$categoryId]);
    }
}
