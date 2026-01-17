<?php

declare(strict_types=1);

namespace App\DTO\Response;

final readonly class SeatResponse
{
    public function __construct(
        public int $id,
        public int $row,
        public int $number,
        public string $status,
        public ?\DateTimeImmutable $expiresAt = null,
    ) {
    }
}
