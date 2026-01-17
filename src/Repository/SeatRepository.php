<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Hall;
use App\Entity\Screening;
use App\Entity\Seat;
use App\Entity\SeatAllocation;
use App\Repository\Interface\SeatRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Seat>
 */
final class SeatRepository extends ServiceEntityRepository implements SeatRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seat::class);
    }

    /**
     * @param list<int> $seatIds
     *
     * @return list<Seat>
     */
    public function findByIdsAndHall(array $seatIds, Hall $hall): array
    {
        $result = $this->createQueryBuilder('s')
            ->where('s.id IN (:ids)')
            ->andWhere('s.hall = :hall')
            ->setParameter('ids', $seatIds)
            ->setParameter('hall', $hall)
            ->getQuery()
            ->getResult();

        /* @var list<Seat> */
        return $result;
    }

    /**
     * @return array<int, array{seat: Seat, allocation: ?SeatAllocation}>
     */
    public function findAllWithAllocationsForScreening(Screening $screening): array
    {
        $seatsResult = $this->createQueryBuilder('seat')
            ->where('seat.hall = :hall')
            ->orderBy('seat.rowNumber', 'ASC')
            ->addOrderBy('seat.seatNumber', 'ASC')
            ->setParameter('hall', $screening->getHall())
            ->getQuery()
            ->getResult();

        /** @var list<Seat> $seats */
        $seats = $seatsResult;

        $allocationsResult = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('sa', 'seat')
            ->from('App\Entity\SeatAllocation', 'sa')
            ->join('sa.seat', 'seat')
            ->where('sa.screening = :screening')
            ->setParameter('screening', $screening)
            ->getQuery()
            ->getResult();

        /** @var list<SeatAllocation> $allocations */
        $allocations = $allocationsResult;

        /** @var array<int, SeatAllocation> $allocationMap */
        $allocationMap = [];
        foreach ($allocations as $allocation) {
            $seatId = $allocation->getSeat()->getId();
            if (null !== $seatId) {
                $allocationMap[$seatId] = $allocation;
            }
        }

        $result = [];
        foreach ($seats as $seat) {
            $seatId = $seat->getId();
            if (null === $seatId) {
                continue;
            }

            $result[] = [
                'seat' => $seat,
                'allocation' => $allocationMap[$seatId] ?? null,
            ];
        }

        return $result;
    }
}
