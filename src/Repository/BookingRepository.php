<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Booking;
use App\Enum\AllocationStatus;
use App\Repository\Interface\BookingRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
final class BookingRepository extends ServiceEntityRepository implements BookingRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function save(Booking $booking): void
    {
        $this->getEntityManager()->persist($booking);
    }

    public function findById(int $id): ?Booking
    {
        $result = $this->createQueryBuilder('b')
            ->select('b', 'allocations', 'seat')
            ->leftJoin('b.allocations', 'allocations')
            ->leftJoin('allocations.seat', 'seat')
            ->where('b.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        return $result instanceof Booking ? $result : null;
    }

    /**
     * @return list<Booking>
     */
    public function findExpiredHeldBookings(): array
    {
        $now = new \DateTimeImmutable();

        $result = $this->createQueryBuilder('b')
            ->where('b.status = :heldStatus')
            ->andWhere('b.expiresAt < :now')
            ->setParameter('heldStatus', AllocationStatus::HELD->value)
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();

        /* @var list<Booking> */
        return $result;
    }
}
