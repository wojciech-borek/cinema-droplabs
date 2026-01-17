<?php

declare(strict_types=1);

namespace App\DTO\Response;

final readonly class AuthResponse
{
    public function __construct(
        public string $token,
        public int $expiresIn,
    ) {
    }
}
