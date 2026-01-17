<?php

declare(strict_types=1);

namespace App\Repository\Interface;

use App\Entity\Booking;

interface BookingRepositoryInterface
{
    public function save(Booking $booking): void;

    public function findById(int $id): ?Booking;

    /**
     * @return list<Booking>
     */
    public function findExpiredHeldBookings(): array;
}
