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
        // sort
        $sqlSort = $this->prepareSqlSort($sorts);

        // where
        // filter (price, name, category)
        // sort (price, name)
        $sqlWhere = '';

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
        '.$sqlWhere.'
        '.$sqlSort.'
        LIMIT
            1
        ';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $results = $stmt->executeQuery()->fetchAllAssociative();

        $ids = [];
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
            $sorts['price'] = 'asc';
        }

        $sqlSort = [];
        foreach ($sorts as $sortName => $sortType) {
            $sqlSort[] = $sortName.' '.$sortType;
        }

        $sqlSort = ' ORDER BY '.implode(',', $sqlSort);

        return $sqlSort;
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
