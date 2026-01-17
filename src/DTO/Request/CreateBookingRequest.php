<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final readonly class CreateBookingRequest
{
    /**
     * @param list<int> $seatIds
     */
    public function __construct(
        #[Assert\NotBlank(message: 'Screening ID cannot be blank')]
        #[Assert\Type(type: 'int', message: 'Screening ID must be an integer')]
        #[Assert\Positive(message: 'Screening ID must be positive')]
        public int $screeningId,
        #[Assert\NotBlank(message: 'Seat IDs cannot be blank')]
        #[Assert\All([
            new Assert\Type(type: 'int', message: 'Each seat ID must be an integer'),
            new Assert\Positive(message: 'Each seat ID must be positive'),
        ])]
        #[Assert\Count(
            min: 1,
            max: 10,
            minMessage: 'At least {{ limit }} seat must be selected',
            maxMessage: 'Maximum {{ limit }} seats can be selected per booking'
        )]
        public array $seatIds,
        #[Assert\NotBlank(message: 'Customer email cannot be blank')]
        #[Assert\Email(message: 'Customer email must be a valid email address')]
        #[Assert\Length(max: 255, maxMessage: 'Customer email cannot be longer than {{ limit }} characters')]
        public string $customerEmail,
    ) {
    }

    #[Assert\Callback]
    public function validateSeatIdsUnique(ExecutionContextInterface $context): void
    {
        if (count($this->seatIds) !== count(array_unique($this->seatIds))) {
            $context->buildViolation('Seat IDs must be unique')
                ->atPath('seatIds')
                ->addViolation();
        }
    }
}
