<?php

namespace App\Service\Paginator\Product;

use App\Entity\Product;
use App\Entity\User;
use App\Service\Paginator\Helper\StatementConverter;
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
        private readonly StatementConverter $paginatorHelper,
    ) {
    }

    public function paginate(array $filterData, int $limit, int $offset): array
    {
        /** @var User $user */
        $user = $this->security->getUser();

        // prepare select
        $sqlSelect = $this->productSqlHelper->prepareSelect($user);

        // prepare filters
        $sqlParams = [];
        $filters = $filterData['filters'] ?? [];
        $sqlFilterName = $this->productSqlHelper->prepareNameFilter($filters, $sqlParams);
        $sqlFilterCategory = $this->productSqlHelper->prepareCategoryFilter($filters, $sqlParams);
        $sqlFilterPrice = $this->productSqlHelper->preparePriceFilter($filters, $sqlParams);

        // prepare sorts
        $sorts = $filterData['sorts'] ?? [];
        $sqlSort = $this->productSqlHelper->prepareSqlSort($sorts);

        // prepare limit offset
        $sqlLimitOffset = ' LIMIT '.$limit.' OFFSET '.$offset.' ';

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

        // prepare statement
        $stmt = $this->entityManager->getConnection()->prepare($sql);

        // bind values
        foreach ($sqlParams as $name => $value) {
            $stmt->bindValue($name, $value);
        }

        // set price_adjusted to entity
        $results = $this->paginatorHelper->paginate(Product::class, $stmt, $sqlParams);
        $products = [];
        foreach ($results as $result) {
            /* @var Product $product */
            $product = $result['entity'];

            $product->setPriceAdjusted($result['result']['price_adjusted']);

            $products[] = $product;
        }

        return $products;
    }
}
