<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly Security $security,
    ) {
        parent::__construct($registry, Product::class);
    }

    public function findByFilters(array $filters, array $sorts)
    {
        $sqlParams = [];

        // filter
        $sqlFilterName = $this->prepareNameFilter($filters, $sqlParams);
        $sqlFilterCategory = $this->prepareCategoryFilter($filters, $sqlParams);
        $sqlFilterPrice = $this->preparePriceFilter($filters, $sqlParams);

        // sort
        $sqlSort = $this->prepareSqlSort($sorts);

        // filter (price, name, category)
        // sort (price, name)

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

    private function prepareSqlSort(array $sorts): string
    {
        if (0 === count($sorts)) {
            $sorts[] = ['price' => 'asc'];
        }

        $sqlSort = [];
        foreach ($sorts as $value) {
            $sortName = array_key_first($value);
            $sortType = $value[$sortName];

            $sqlSort[] = $sortName.' '.$sortType;
        }

        $sqlSort = ' ORDER BY '.implode(',', $sqlSort);

        return $sqlSort;
    }

    private function preparePriceFilter(array $filters, array &$sqlParams): string
    {
        if (!empty($value = $filters[0]['price']['lte'] ?? null)) {
            $sqlParams['price'] = $value;

            return ' HAVING price_adjusted < :price';
        }

        return '';
    }

    private function prepareCategoryFilter(array $filters, array &$sqlParams): string
    {
        if (!empty($value = $filters[0]['category']['equals'] ?? null)) {
            $sqlParams['category'] = $value;

            return '        AND EXISTS (
                SELECT * FROM product_category pc where pc.category_id in (:category) and pc.product_id = p.id
            ) ';
        }

        return '';
    }

    private function prepareNameFilter(array $filters, array &$sqlParams): string
    {
        if (!empty($value = $filters[0]['name']['starts_with'] ?? null)) {
            $sqlParams['name'] = $value.'%';

            return ' AND name LIKE :name  ';
        }

        return '';
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
