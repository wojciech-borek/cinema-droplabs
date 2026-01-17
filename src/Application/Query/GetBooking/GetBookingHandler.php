<?php

declare(strict_types=1);

namespace App\Application\Query\GetBooking;

use App\DTO\Response\BookingResponse;
use App\Exception\EntityNotFoundException;
use App\Mapper\BookingMapper;
use App\Repository\Interface\BookingRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query_bus')]
final readonly class GetBookingHandler
{
    public function __construct(
        private BookingRepositoryInterface $bookingRepository,
    ) {
    }

    public function __invoke(GetBookingQuery $query): BookingResponse
    {
        $booking = $this->bookingRepository->findById($query->bookingId);

        if (null === $booking) {
            throw new EntityNotFoundException('Booking', $query->bookingId);
        }

        return BookingMapper::mapToResponse($booking);
    }
}
