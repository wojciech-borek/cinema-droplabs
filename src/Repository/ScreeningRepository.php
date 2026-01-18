<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Screening;
use App\Repository\Interface\ScreeningRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Screening>
 */
final class ScreeningRepository extends ServiceEntityRepository implements ScreeningRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Screening::class);
    }

    public function findOneByIdWithLock(int $id): ?Screening
    {
        $result = $this->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)
            ->getOneOrNullResult();

        return $result instanceof Screening ? $result : null;
    }

    public function findById(int $id): ?Screening
    {
        $result = $this->find($id);

        return $result instanceof Screening ? $result : null;
    }

    /**
     * @return array{data: array<Screening>, total: int}
     */
    public function findAllPaginated(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.hall', 'h')
            ->where('h.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('s.startsAt', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        /** @var list<Screening> $screenings */
        $screenings = $qb->getQuery()->getResult();

        $countQb = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->leftJoin('s.hall', 'h')
            ->where('h.isActive = :isActive')
            ->setParameter('isActive', true);

        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        return [
            'data' => $screenings,
            'total' => $total,
        ];
    }

    public function save(Screening $screening): void
    {
        $this->getEntityManager()->persist($screening);
    }
}
