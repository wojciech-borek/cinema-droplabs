<?php

declare(strict_types=1);

namespace App\Exception;

final class SeatsUnavailableException extends \RuntimeException implements ApplicationException
{
    /**
     * @param list<int> $unavailableSeatIds
     */
    public function __construct(private readonly array $unavailableSeatIds)
    {
        parent::__construct(
            sprintf('Seats %s are not available', implode(', ', $this->unavailableSeatIds))
        );
    }

    /**
     * @return list<int>
     */
    public function getUnavailableSeatIds(): array
    {
        return $this->unavailableSeatIds;
    }
}
