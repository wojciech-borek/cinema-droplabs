<?php

declare(strict_types=1);

namespace App\DTO\Response;

/**
 * @property SeatResponse[] $seats
 */
final readonly class BookingResponse
{
    /**
     * @param SeatResponse[] $seats
     */
    public function __construct(
        public int $id,
        public int $screeningId,
        public string $customerEmail,
        public string $status,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $expiresAt,
        public array $seats,
    ) {
    }
}
