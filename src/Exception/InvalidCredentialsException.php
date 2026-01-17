<?php

declare(strict_types=1);

namespace App\Exception;

final class InvalidCredentialsException extends \RuntimeException implements ApplicationException
{
    public static function create(): self
    {
        return new self('Invalid email or password');
    }
}
