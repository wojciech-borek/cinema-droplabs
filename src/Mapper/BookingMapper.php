<?php

declare(strict_types=1);

namespace App\Mapper;

use App\DTO\Response\BookingResponse;
use App\Entity\Booking;
use App\Exception\EntityIdGenerationException;

final class BookingMapper
{
    public static function mapToResponse(Booking $booking): BookingResponse
    {
        $seats = SeatMapper::mapFromAllocations(
            $booking->getAllocations()->toArray()
        );

        return new BookingResponse(
            id: $booking->getId() ?? throw new EntityIdGenerationException('Booking'),
            screeningId: $booking->getScreening()->getId() ?? throw new EntityIdGenerationException('Screening'),
            customerEmail: $booking->getCustomerEmail(),
            status: $booking->getStatus(),
            createdAt: $booking->getCreatedAt(),
            expiresAt: $booking->getExpiresAt(),
            seats: $seats
        );
    }
}
