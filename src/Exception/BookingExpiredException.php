<?php

declare(strict_types=1);

namespace App\Exception;

final class BookingExpiredException extends \RuntimeException implements ApplicationException
{
    public function __construct(int $bookingId)
    {
        parent::__construct(
            sprintf('Booking %d has expired', $bookingId)
        );
    }
}
