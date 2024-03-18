<?php

namespace App\Service\Paginator\Helper;

use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManagerInterface;

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
            ->createQueryBuilder('p')
            ->where('p.id in (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('FIELD(p.id,'.implode(',', $ids).')');

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
