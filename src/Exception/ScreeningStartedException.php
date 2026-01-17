<?php

declare(strict_types=1);

namespace App\Exception;

final class ScreeningStartedException extends \RuntimeException implements ApplicationException
{
    public function __construct(int $screeningId)
    {
        parent::__construct(
            sprintf('Cannot book seats for screening %d that has already started', $screeningId)
        );
    }

    public function getScreeningId(): int
    {
        $parts = explode(' ', $this->getMessage());

        return (int) $parts[5];
    }
}
