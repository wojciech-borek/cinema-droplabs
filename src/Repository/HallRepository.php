<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Hall;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hall>
 */
final class HallRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hall::class);
    }

    public function findActiveById(int $id): ?Hall
    {
        $hall = $this->createQueryBuilder('h')
            ->where('h.id = :id')
            ->andWhere('h.isActive = :isActive')
            ->setParameter('id', $id)
            ->setParameter('isActive', true)
            ->getQuery()
            ->getOneOrNullResult();

        return $hall instanceof Hall ? $hall : null;
    }

    /**
     * @return array{data: array<Hall>, total: int}
     */
    public function findActivePaginated(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        $qb = $this->createQueryBuilder('h')
            ->where('h.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('h.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        /** @var list<Hall> $halls */
        $halls = $qb->getQuery()->getResult();

        $countQb = $this->createQueryBuilder('h')
            ->select('COUNT(h.id)')
            ->where('h.isActive = :isActive')
            ->setParameter('isActive', true);

        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        return [
            'data' => $halls,
            'total' => $total,
        ];
    }

    public function save(Hall $hall): void
    {
        $this->getEntityManager()->persist($hall);
    }
}
