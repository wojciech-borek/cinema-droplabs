<?php

declare(strict_types=1);

namespace App\Application\Query\GetHall;

final readonly class GetHallQuery
{
    public function __construct(
        public int $id,
    ) {
    }
}
