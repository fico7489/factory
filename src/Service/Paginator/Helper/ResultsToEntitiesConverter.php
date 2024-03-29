<?php

namespace App\Service\Paginator\Helper;

use Doctrine\ORM\EntityManagerInterface;

// helper to convert raw results from DB of entities, it can be used in more sql paginator implementations
// entities are returned with raw results from DB so that we can call custom entity setters (like price_adjusted)
class ResultsToEntitiesConverter
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function convert(string $className, array $results): array
    {
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
