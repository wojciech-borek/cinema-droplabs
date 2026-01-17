<?php

declare(strict_types=1);

namespace App\Application\Query\GetAvailableSeats;

final readonly class GetAvailableSeatsQuery
{
    public function __construct(
        public int $screeningId,
    ) {
    }
}
