<?php

declare(strict_types=1);

namespace App\DTO\Response;

final readonly class ScreeningListResponse
{
    /**
     * @param array<ScreeningResponse> $data
     */
    public function __construct(
        public array $data,
        public PaginationMeta $meta,
    ) {
    }
}
