<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\User;
use App\Service\Paginator\Paginator;
use App\Service\Paginator\Product\ProductSql;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly Security $security,
        private readonly ProductSql $productSql,
        private readonly Paginator $paginator,
    ) {
        parent::__construct($registry, Product::class);
    }

    public function paginateByFilters($filterData): array
    {
        /** @var User $user */
        $user = $this->security->getUser();

        //prepare price where
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

        $products = $this->paginator->paginate(Product::class, $sql, $sqlParams);

        return $products;
    }
}
