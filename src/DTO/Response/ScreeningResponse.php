<?php

declare(strict_types=1);

namespace App\DTO\Response;

final readonly class ScreeningResponse
{
    public function __construct(
        public int $id,
        public string $hallName,
        public string $movieTitle,
        public \DateTimeImmutable $startsAt,
        public int $availableSeatsCount,
    ) {
    }
}
