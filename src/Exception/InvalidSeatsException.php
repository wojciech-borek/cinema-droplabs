<?php

declare(strict_types=1);

namespace App\Exception;

final class InvalidSeatsException extends \RuntimeException implements ApplicationException
{
    public function __construct(string $message = 'Invalid seat selection')
    {
        parent::__construct($message);
    }
}
