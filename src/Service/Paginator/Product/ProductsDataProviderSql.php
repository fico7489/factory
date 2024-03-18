<?php

namespace App\Service\Paginator\Product;

use App\Entity\Product;
use App\Entity\User;
use App\Service\Paginator\Helper\StatementConverter;
use App\Service\Paginator\Interface\DataProviderInterface;
use App\Service\Paginator\Product\Sql\SqlHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ProductsDataProviderSql implements DataProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly SqlHelper $productSql,
        private readonly StatementConverter $paginatorHelper,
    ) {
    }

    public function paginate(array $filterData): array
    {
        /** @var User $user */
        $user = $this->security->getUser();

        // prepare price where
        $userId = $user->getUserIdentifier();
        $userGroupIds = $this->productSql->prepareUserGroupIds($user);

        // prepare filters
        $sqlParams = [];
        $filters = $filterData['filters'] ?? [];
        $sqlFilterName = $this->productSql->prepareNameFilter($filters, $sqlParams);
        $sqlFilterCategory = $this->productSql->prepareCategoryFilter($filters, $sqlParams);
        $sqlFilterPrice = $this->productSql->preparePriceFilter($filters, $sqlParams);

        // prepare sorts
        $sorts = $filterData['sorts'] ?? [];
        $sqlSort = $this->productSql->prepareSqlSort($sorts);

        $sql = '
        SELECT
            *,
             (
                    select min(price) from (
                        (SELECT p.price)
                        UNION
                        (SELECT min(pcl.price) as price FROM product_contract_list pcl WHERE pcl.sku = p.sku AND pcl.user_id = '.$userId.')
                        UNION
                        (SELECT min(ppl.price) as price FROM product_price_list ppl WHERE p.sku = ppl.sku AND ppl.user_group_id in ('.$userGroupIds.'))
                    ) as price_adjusted
                ) as price_adjusted
            FROM
            product p
        WHERE 1 = 1
        '.$sqlFilterCategory.'
        '.$sqlFilterName.'
        '.$sqlFilterPrice.'
        '.$sqlSort.'
        LIMIT
            10
        ';

        // TODO limit from api platform

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
