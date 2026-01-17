<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class HallUpdateRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Name cannot be blank')]
        #[Assert\Length(max: 255, maxMessage: 'Name cannot be longer than {{ limit }} characters')]
        public string $name,
    ) {
    }
}
