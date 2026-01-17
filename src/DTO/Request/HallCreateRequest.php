<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final readonly class HallCreateRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Name cannot be blank')]
        #[Assert\Length(max: 255, maxMessage: 'Name cannot be longer than {{ limit }} characters')]
        public string $name,
        #[Assert\NotBlank(message: 'Rows count cannot be blank')]
        #[Assert\Type(type: 'int', message: 'Rows count must be an integer')]
        #[Assert\Range(
            min: 1,
            max: 100,
            notInRangeMessage: 'Rows count must be between {{ min }} and {{ max }}'
        )]
        public int $rowsCount,
        #[Assert\NotBlank(message: 'Seats per row cannot be blank')]
        #[Assert\Type(type: 'int', message: 'Seats per row must be an integer')]
        #[Assert\Range(
            min: 1,
            max: 100,
            notInRangeMessage: 'Seats per row must be between {{ min }} and {{ max }}'
        )]
        public int $seatsPerRow,
    ) {
    }

    #[Assert\Callback]
    public function validateTotalSeats(ExecutionContextInterface $context): void
    {
        $totalSeats = $this->rowsCount * $this->seatsPerRow;

        if ($totalSeats > 2000) {
            $context->buildViolation('Total seats (rowsCount * seatsPerRow) cannot exceed 2000')
                ->atPath('rowsCount')
                ->addViolation();
        }
    }
}
