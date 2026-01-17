<?php

declare(strict_types=1);

namespace App\Application\Command\UpdateHall;

final readonly class UpdateHallCommand
{
    public function __construct(
        public int $id,
        public string $name,
    ) {
    }
}
