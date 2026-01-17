<?php

declare(strict_types=1);

namespace App\DTO\Response;

final readonly class PaginationMeta
{
    public function __construct(
        public int $currentPage,
        public int $itemsPerPage,
        public int $totalItems,
        public int $totalPages,
    ) {
    }
}
