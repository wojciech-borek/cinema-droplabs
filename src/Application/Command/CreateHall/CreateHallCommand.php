<?php

declare(strict_types=1);

namespace App\Application\Command\CreateHall;

final readonly class CreateHallCommand
{
    public function __construct(
        public string $name,
        public int $rowsCount,
        public int $seatsPerRow,
    ) {
    }
}
