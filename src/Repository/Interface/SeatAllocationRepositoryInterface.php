<?php

declare(strict_types=1);

namespace App\Repository\Interface;

use App\Entity\Screening;

interface SeatAllocationRepositoryInterface
{
    /**
     * @param list<int> $seatIds
     *
     * @return list<int>
     */
    public function findUnavailableSeats(Screening $screening, array $seatIds): array;
}
