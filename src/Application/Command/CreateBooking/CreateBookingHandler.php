<?php

declare(strict_types=1);

namespace App\Application\Command\CreateBooking;

use App\Entity\Booking;
use App\Entity\SeatAllocation;
use App\Enum\AllocationStatus;
use App\Exception\EntityIdGenerationException;
use App\Exception\EntityNotFoundException;
use App\Exception\InvalidSeatsException;
use App\Exception\ScreeningStartedException;
use App\Exception\SeatsUnavailableException;
use App\Repository\Interface\ScreeningRepositoryInterface;
use App\Repository\Interface\SeatAllocationRepositoryInterface;
use App\Repository\Interface\SeatRepositoryInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command_bus')]
final readonly class CreateBookingHandler
{
    private const HOLD_DURATION_MINUTES = 10;

    public function __construct(
        private ScreeningRepositoryInterface $screeningRepository,
        private SeatAllocationRepositoryInterface $allocationRepository,
        private SeatRepositoryInterface $seatRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(CreateBookingCommand $command): int
    {
        $screening = $this->screeningRepository->findOneByIdWithLock($command->screeningId);

        if (null === $screening) {
            throw new EntityNotFoundException('Screening', $command->screeningId);
        }

        if ($screening->hasStarted()) {
            throw new ScreeningStartedException($command->screeningId);
        }

        $validSeats = $this->seatRepository->findByIdsAndHall(
            $command->seatIds,
            $screening->getHall()
        );

        if (count($validSeats) !== count($command->seatIds)) {
            throw new InvalidSeatsException('Some seats do not belong to this screening hall or do not exist');
        }

        $unavailableSeats = $this->allocationRepository->findUnavailableSeats(
            $screening,
            $command->seatIds
        );

        if (!empty($unavailableSeats)) {
            throw new SeatsUnavailableException($unavailableSeats);
        }

        $expiresAt = new \DateTimeImmutable('+'.self::HOLD_DURATION_MINUTES.' minutes');

        $booking = new Booking(
            screening: $screening,
            customerEmail: $command->customerEmail,
            expiresAt: $expiresAt
        );

        $this->em->persist($booking);

        foreach ($validSeats as $seat) {
            $allocation = new SeatAllocation(
                screening: $screening,
                seat: $seat,
                booking: $booking,
                status: AllocationStatus::HELD,
                expiresAt: $expiresAt
            );

            $this->em->persist($allocation);
            $booking->addAllocation($allocation);
        }

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new SeatsUnavailableException($command->seatIds);
        }

        return $booking->getId() ?? throw new EntityIdGenerationException('Booking');
    }
}
