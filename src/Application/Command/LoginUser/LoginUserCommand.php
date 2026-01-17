<?php

declare(strict_types=1);

namespace App\Application\Command\LoginUser;

use App\ValueObject\EmailAddress;

final readonly class LoginUserCommand
{
    public function __construct(
        public EmailAddress $email,
        public string $password,
    ) {
    }
}
