<?php

declare(strict_types=1);

namespace App\Repository\Interface;

use App\Entity\Hall;
use App\Entity\Screening;
use App\Entity\Seat;

interface SeatRepositoryInterface
{
    /**
     * @return array<int, array{seat: Seat, allocation: ?\App\Entity\SeatAllocation}>
     */
    public function findAllWithAllocationsForScreening(Screening $screening): array;

    /**
     * @param list<int> $seatIds
     *
     * @return list<Seat>
     */
    public function findByIdsAndHall(array $seatIds, Hall $hall): array;
}
