<?php

declare(strict_types=1);

namespace App\Application\Command\DeleteHall;

final readonly class DeleteHallCommand
{
    public function __construct(
        public int $id,
    ) {
    }
}
