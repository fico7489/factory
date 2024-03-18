<?php

namespace App\Service\Paginator\Helper;

use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManagerInterface;

// helper to convert sql from statement to array of entities and results, it can be used in more sql paginator implementations
// entities are returned with raw results from DB so that we can call custom entity setters (like price_adjusted)
class StatementConverter
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function paginate(string $className, Statement $stmt, $sqlParams): array
    {
        $results = $stmt->executeQuery()->fetchAllAssociative();

        $ids = [0];
        $resultsArray = [];
        foreach ($results as $result) {
            $id = $result['id'];

            $resultsArray[$id] = $result;
            $ids[] = $id;
        }

        $queryBuilder = $this
            ->entityManager
            ->getRepository($className)
            ->createQueryBuilder('o')
            ->where('o.id in (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('FIELD(o.id,'.implode(',', $ids).')');

        $entities = $queryBuilder->getQuery()->getResult();

        $resultsFinal = [];
        foreach ($entities as $entity) {
            $resultsFinal[] = [
                'entity' => $entity,
                'result' => $resultsArray[$entity->getId()],
            ];
        }

        return $resultsFinal;
    }
}
