<?php

declare(strict_types=1);

namespace App\Exception;

final class ScreeningNotFoundException extends EntityNotFoundException
{
    public static function create(int $screeningId): self
    {
        return new self('Screening', $screeningId);
    }
}
