<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    )
    {
        parent::__construct($registry, Product::class);
    }

    public function findByFilters(array $filters, array $sorts)
    {
        //sort
        $sqlSort = $this->prepareSqlSort();


        //where
        //filter (price, name, category)
        //sort (price, name)
        $sqlWhere = '';

        $sql = '
        SELECT
            *,
             (
                    select min(price) from (
                        (SELECT p.price)
                        UNION
                        (SELECT min(pcl.price) as price FROM product_contract_list pcl WHERE pcl.sku = p.sku AND pcl.user_id = 1)
                        UNION
                        (SELECT min(ppl.price) as price FROM product_price_list ppl WHERE p.sku = ppl.sku AND ppl.user_group_id = 1)
                    ) as price_min
                ) as price_min
            FROM
            product p
        WHERE 1 = 1
        ' . $sqlWhere . '
        ' . $sqlSort . '
        LIMIT
            15
        ';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $results = $stmt->executeQuery()->fetchAllAssociative();

        $ids = [];
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }

        $queryBuilder = $this
            ->createQueryBuilder('p')
            ->where('p.id in (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('FIELD(p.id,' . implode(',', $ids) . ')');

        $products = $queryBuilder->getQuery()->getResult();

        return $products;
    }

    private function prepareSqlSort() : string
    {
        if (count($sorts) === 0) {
            $sorts['price'] = 'asc';
        }

        $sqlSort = [];
        foreach ($sorts as $sortName => $sortType) {
            $sqlSort[] = $sortName . ' ' . $sortType;
        }

        $sqlSort = ' ORDER BY ' . implode(',', $sqlSort);

        return $sqlSort;
    }
}
