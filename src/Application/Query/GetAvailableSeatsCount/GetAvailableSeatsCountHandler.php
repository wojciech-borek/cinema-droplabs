<?php

declare(strict_types=1);

namespace App\Application\Query\GetAvailableSeatsCount;

use App\Enum\SeatStatus;
use App\Exception\EntityNotFoundException;
use App\Repository\Interface\ScreeningRepositoryInterface;
use App\Repository\Interface\SeatRepositoryInterface;
use App\Service\Interface\SeatStatusResolverInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query_bus')]
final readonly class GetAvailableSeatsCountHandler
{
    public function __construct(
        private ScreeningRepositoryInterface $screeningRepository,
        private SeatRepositoryInterface $seatRepository,
        private SeatStatusResolverInterface $seatStatusResolver,
    ) {
    }

    public function __invoke(GetAvailableSeatsCountQuery $query): int
    {
        $screening = $this->screeningRepository->findById($query->screeningId);

        if (null === $screening) {
            throw new EntityNotFoundException('Screening', $query->screeningId);
        }

        $seatsWithAllocations = $this->seatRepository->findAllWithAllocationsForScreening($screening);

        $now = new \DateTimeImmutable();
        $availableCount = 0;

        foreach ($seatsWithAllocations as $item) {
            $statusData = $this->seatStatusResolver->resolveStatus($item['allocation'], $now);

            if ($statusData['status'] === SeatStatus::AVAILABLE->value) {
                ++$availableCount;
            }
        }

        return $availableCount;
    }
}
