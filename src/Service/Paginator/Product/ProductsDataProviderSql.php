<?php

namespace App\Service\Paginator\Product;

use App\Entity\Product;
use App\Entity\User;
use App\Service\Paginator\Helper\ResultsToEntitiesConverter;
use App\Service\Paginator\Interface\DataProviderInterface;
use App\Service\Paginator\Product\Sql\ProductSqlHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ProductsDataProviderSql implements DataProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly ProductSqlHelper $productSqlHelper,
        private readonly ResultsToEntitiesConverter $resultsToEntitiesConverter,
    ) {
    }

    public function count(array $filterData): int
    {
        // get results
        $results = $this->getResults($filterData, 0, 0, true);

        // fetch count
        $count = $results[0]['count(*)'] ?? 0;

        return $count;
    }

    public function entities(array $filterData, int $limit, int $offset): array
    {
        // get results
        $results = $this->getResults($filterData, $limit, $offset, false);

        // convert to entities
        $results = $this->resultsToEntitiesConverter->convert(Product::class, $results);

        // set price_adjusted for each entity
        $products = [];
        foreach ($results as $result) {
            /* @var Product $product */
            $product = $result['entity'];

            $product->setPriceAdjusted($result['result']['price_adjusted']);

            $products[] = $product;
        }

        return $products;
    }

    public function getResults(array $filterData, int $limit, int $offset, bool $count): array
    {
        /** @var User $user */
        $user = $this->security->getUser();

        // prepare select
        $sqlSelect = $this->productSqlHelper->prepareSelect($user);

        // prepare limit offset
        $sqlLimitOffset = ' LIMIT '.$limit.' OFFSET '.$offset.' ';
        if ($count) {
            // for pagination count take all
            $sqlLimitOffset = '';
        }

        // prepare filters
        $sqlParams = [];
        $filters = $filterData['filters'] ?? [];
        $sqlFilterName = $this->productSqlHelper->prepareNameFilter($filters, $sqlParams);
        $sqlFilterCategory = $this->productSqlHelper->prepareCategoryFilter($filters, $sqlParams);
        $sqlFilterPrice = $this->productSqlHelper->preparePriceFilter($filters, $sqlParams);

        // prepare sorts
        $sorts = $filterData['sorts'] ?? [];
        $sqlSort = $this->productSqlHelper->prepareSqlSort($sorts);

        // select min price from all 3 price sources(product, product_contract_list and product_price_list)
        $sql = '
            '.$sqlSelect.'
            FROM
            product p
        WHERE 1 = 1
            '.$sqlFilterCategory.'
            '.$sqlFilterName.'
            '.$sqlFilterPrice.'
            '.$sqlSort.'
            '.$sqlLimitOffset.'
        ';

        if ($count) {
            // for pagination count fetch count
            $sql = ' SELECT count(*) FROM ('.$sql.') as count ';
        }

        // prepare statement
        $stmt = $this->entityManager->getConnection()->prepare($sql);

        // bind values
        foreach ($sqlParams as $name => $value) {
            $stmt->bindValue($name, $value);
        }

        $results = $stmt->executeQuery()->fetchAllAssociative();

        return $results;
    }
}
