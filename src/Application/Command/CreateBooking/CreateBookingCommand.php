<?php

declare(strict_types=1);

namespace App\Application\Command\CreateBooking;

final readonly class CreateBookingCommand
{
    /**
     * @param list<int> $seatIds
     */
    public function __construct(
        public int $screeningId,
        public array $seatIds,
        public string $customerEmail,
    ) {
    }
}
