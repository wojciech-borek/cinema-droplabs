<?php

declare(strict_types=1);

namespace App\DTO\Response;

final readonly class HallListResponse
{
    /**
     * @param array<HallResponse> $data
     */
    public function __construct(
        public array $data,
        public PaginationMeta $meta,
    ) {
    }
}
