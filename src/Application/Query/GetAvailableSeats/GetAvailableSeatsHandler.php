<?php

declare(strict_types=1);

namespace App\Application\Query\GetAvailableSeats;

use App\DTO\Response\AvailableSeatsResponse;
use App\Exception\EntityIdGenerationException;
use App\Exception\EntityNotFoundException;
use App\Mapper\SeatMapper;
use App\Repository\Interface\ScreeningRepositoryInterface;
use App\Repository\Interface\SeatRepositoryInterface;
use App\Service\Interface\SeatStatusResolverInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query_bus')]
final readonly class GetAvailableSeatsHandler
{
    public function __construct(
        private ScreeningRepositoryInterface $screeningRepository,
        private SeatRepositoryInterface $seatRepository,
        private SeatStatusResolverInterface $seatStatusResolver,
    ) {
    }

    public function __invoke(GetAvailableSeatsQuery $query): AvailableSeatsResponse
    {
        $screening = $this->screeningRepository->findById($query->screeningId);

        if (null === $screening) {
            throw new EntityNotFoundException('Screening', $query->screeningId);
        }

        $seatsWithAllocations = $this->seatRepository->findAllWithAllocationsForScreening($screening);

        $now = new \DateTimeImmutable();
        $seatsWithStatus = array_map(
            function (array $item) use ($now) {
                $statusData = $this->seatStatusResolver->resolveStatus($item['allocation'], $now);

                return [
                    'seat' => $item['seat'],
                    'status' => $statusData['status'],
                    'expiresAt' => $statusData['expiresAt'],
                ];
            },
            $seatsWithAllocations
        );

        $seats = SeatMapper::mapFromDataArray($seatsWithStatus);

        return new AvailableSeatsResponse(
            screeningId: $screening->getId() ?? throw new EntityIdGenerationException('Screening'),
            hallName: $screening->getHall()->getName(),
            movieTitle: $screening->getMovieTitle()->toString(),
            startsAt: $screening->getStartsAt(),
            seats: $seats
        );
    }
}
