<?php

declare(strict_types=1);

namespace App\Application\Query\GetAvailableSeatsCount;

final readonly class GetAvailableSeatsCountQuery
{
    public function __construct(
        public int $screeningId,
    ) {
    }
}
