<?php

namespace App\Service\Paginator;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class Paginator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function paginate(string $className, string $sql, $sqlParams): array
    {
        $stmt = $this->entityManager->getConnection()->prepare($sql);

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
            ->entityManager
            ->getRepository($className)
            ->createQueryBuilder('p')
            ->where('p.id in (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('FIELD(p.id,'.implode(',', $ids).')');

        $entities = $queryBuilder->getQuery()->getResult();

        foreach ($entities as $entity) {
            /* @var Product $product */
            $entity->setPriceAdjusted($pricesAdjusted[$entity->getId()]);
        }

        return $entities;
    }
}
