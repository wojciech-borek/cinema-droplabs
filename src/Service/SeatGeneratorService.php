<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Hall;
use App\Entity\Seat;

final class SeatGeneratorService
{
    public function generate(Hall $hall, int $rowsCount, int $seatsPerRow): void
    {
        for ($row = 1; $row <= $rowsCount; ++$row) {
            for ($seatNumber = 1; $seatNumber <= $seatsPerRow; ++$seatNumber) {
                $seat = new Seat(
                    hall: $hall,
                    rowNumber: $row,
                    seatNumber: $seatNumber
                );
                $hall->addSeat($seat);
            }
        }
    }
}
