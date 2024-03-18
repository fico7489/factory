<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\User;
use App\Service\Product\ProductSql;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry             $registry,
        private readonly Security   $security,
        private readonly ProductSql $productSql,
    ) {
        parent::__construct($registry, Product::class);
    }

    public function findByFilters(array $filters, array $sorts)
    {
        $sqlParams = [];

        // prepare filters
        $sqlFilterName = $this->productSql->prepareNameFilter($filters, $sqlParams);
        $sqlFilterCategory = $this->productSql->prepareCategoryFilter($filters, $sqlParams);
        $sqlFilterPrice = $this->productSql->preparePriceFilter($filters, $sqlParams);

        // prepare sorts
        $sqlSort = $this->productSql->prepareSqlSort($sorts);

        /** @var User $user */
        $user = $this->security->getUser();
        $userId = $user->getUserIdentifier();
        $userGroupIds = $this->prepareUserGroupIds($user);

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

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $params['name'] = 'aaaa_Firs%';

        foreach ($sqlParams as $name => $value) {
            $stmt->bindValue($name, $value);
        }

        $results = $stmt->executeQuery()->fetchAllAssociative();

        $ids = [0];
        $pricesAdjusted = [];
        foreach ($results as $result) {
            $id = $result['id'];
            $priceAdjusted = $result['price_adjusted'];

            $ids[] = $id;
            $pricesAdjusted[$id] = $priceAdjusted;
        }

        $queryBuilder = $this
            ->createQueryBuilder('p')
            ->where('p.id in (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('FIELD(p.id,'.implode(',', $ids).')');

        $products = $queryBuilder->getQuery()->getResult();

        foreach ($products as $product) {
            /* @var Product $product */
            $product->setPriceAdjusted($pricesAdjusted[$product->getId()]);
        }

        return $products;
    }

    private function prepareUserGroupIds(User $user): string
    {
        $userGroups = $user->getUserGroups()->toArray();

        $userGroupIds = [-1];
        foreach ($userGroups as $userGroup) {
            $userGroupIds[] = $userGroup->getId();
        }
        $userGroupIds = implode(',', $userGroupIds);

        return $userGroupIds;
    }
}
