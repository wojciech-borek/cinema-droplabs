<?php

declare(strict_types=1);

namespace App\Application\Query\ListScreenings;

final readonly class ListScreeningsQuery
{
    public function __construct(
        public int $page,
        public int $limit,
    ) {
    }
}
