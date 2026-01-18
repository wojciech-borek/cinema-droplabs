<?php

declare(strict_types=1);

namespace App\Mapper;

use App\DTO\Response\BookingResponse;
use App\Entity\Booking;
use App\Exception\EntityIdGenerationException;

final class BookingMapper
{
    public function __construct(
        private readonly SeatMapper $seatMapper,
    ) {
    }

    public function mapToResponse(Booking $booking): BookingResponse
    {
        $seats = $this->seatMapper->mapFromAllocations(
            $booking->getAllocations()->toArray()
        );

        return new BookingResponse(
            id: $booking->getId() ?? throw new EntityIdGenerationException('Booking'),
            screeningId: $booking->getScreening()->getId() ?? throw new EntityIdGenerationException('Screening'),
            customerEmail: $booking->getCustomerEmail()->toString(),
            status: $booking->getStatus(),
            createdAt: $booking->getCreatedAt(),
            expiresAt: $booking->getExpiresAt(),
            seats: $seats
        );
    }
}
