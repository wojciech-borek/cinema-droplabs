<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Screening;
use App\Entity\SeatAllocation;
use App\Enum\AllocationStatus;
use App\Repository\Interface\SeatAllocationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SeatAllocation>
 */
final class SeatAllocationRepository extends ServiceEntityRepository implements SeatAllocationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SeatAllocation::class);
    }

    /**
     * @param list<int> $seatIds
     *
     * @return list<int>
     */
    public function findUnavailableSeats(Screening $screening, array $seatIds): array
    {
        $now = new \DateTimeImmutable();

        $qb = $this->createQueryBuilder('sa');
        $qb->select('IDENTITY(sa.seat) as seatId')
            ->where('sa.screening = :screening')
            ->andWhere('IDENTITY(sa.seat) IN (:seatIds)')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('sa.status', ':confirmedStatus'),
                    $qb->expr()->andX(
                        $qb->expr()->eq('sa.status', ':heldStatus'),
                        $qb->expr()->gt('sa.expiresAt', ':now')
                    )
                )
            )
            ->setParameter('screening', $screening)
            ->setParameter('seatIds', $seatIds)
            ->setParameter('confirmedStatus', AllocationStatus::CONFIRMED->value)
            ->setParameter('heldStatus', AllocationStatus::HELD->value)
            ->setParameter('now', $now);

        $result = $qb->getQuery()->getArrayResult();

        /** @var list<array{seatId: int|string}> $result */
        $mapped = array_map(fn (array $row): int => (int) $row['seatId'], $result);

        /* @var list<int> */
        return $mapped;
    }
}
