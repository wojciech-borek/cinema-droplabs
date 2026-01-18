<?php

declare(strict_types=1);

namespace App\Mapper;

use App\DTO\Response\SeatResponse;
use App\Entity\SeatAllocation;
use App\Enum\AllocationStatus;
use App\Enum\SeatStatus;
use App\Exception\EntityIdGenerationException;

final class SeatMapper
{
    public function mapFromAllocation(SeatAllocation $allocation): SeatResponse
    {
        $status = match ($allocation->getStatus()) {
            AllocationStatus::CONFIRMED->value => SeatStatus::CONFIRMED->value,
            AllocationStatus::HELD->value => SeatStatus::HELD->value,
            default => SeatStatus::AVAILABLE->value,
        };

        return new SeatResponse(
            id: $allocation->getSeat()->getId() ?? throw new EntityIdGenerationException('Seat'),
            row: $allocation->getSeat()->getRowNumber(),
            number: $allocation->getSeat()->getSeatNumber(),
            status: $status,
            expiresAt: $allocation->getExpiresAt()
        );
    }

    /**
     * @param SeatAllocation[] $allocations
     *
     * @return SeatResponse[]
     */
    public function mapFromAllocations(array $allocations): array
    {
        return array_map(
            fn (SeatAllocation $allocation) => $this->mapFromAllocation($allocation),
            $allocations
        );
    }

    /**
     * @param array<array{seat: \App\Entity\Seat, status: string, expiresAt: ?\DateTimeImmutable}> $data
     *
     * @return SeatResponse[]
     */
    public function mapFromDataArray(array $data): array
    {
        return array_map(
            fn (array $item) => new SeatResponse(
                id: $item['seat']->getId() ?? throw new EntityIdGenerationException('Seat'),
                row: $item['seat']->getRowNumber(),
                number: $item['seat']->getSeatNumber(),
                status: $item['status'],
                expiresAt: $item['expiresAt']
            ),
            $data
        );
    }
}
