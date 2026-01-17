<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class PaginationRequest
{
    public function __construct(
        #[Assert\Positive(message: 'Page must be a positive integer')]
        public int $page = 1,
        #[Assert\Positive(message: 'Limit must be a positive integer')]
        #[Assert\LessThanOrEqual(value: 100, message: 'Limit cannot exceed 100')]
        public int $limit = 20,
    ) {
    }
}
