<?php

declare(strict_types=1);

namespace App\DTO\Response;

/**
 * @property SeatResponse[] $seats
 */
final readonly class AvailableSeatsResponse
{
    /**
     * @param SeatResponse[] $seats
     */
    public function __construct(
        public int $screeningId,
        public string $hallName,
        public string $movieTitle,
        public \DateTimeImmutable $startsAt,
        public array $seats,
    ) {
    }
}
