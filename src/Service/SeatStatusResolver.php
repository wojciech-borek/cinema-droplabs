<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\SeatAllocation;
use App\Enum\AllocationStatus;
use App\Enum\SeatStatus;
use App\Service\Interface\SeatStatusResolverInterface;

final readonly class SeatStatusResolver implements SeatStatusResolverInterface
{
    /**
     * @return array{status: string, expiresAt: ?\DateTimeImmutable}
     */
    public function resolveStatus(?SeatAllocation $allocation, \DateTimeImmutable $now): array
    {
        if (null === $allocation) {
            return [
                'status' => SeatStatus::AVAILABLE->value,
                'expiresAt' => null,
            ];
        }

        if ($allocation->getStatus() === AllocationStatus::CONFIRMED->value) {
            return [
                'status' => SeatStatus::CONFIRMED->value,
                'expiresAt' => null,
            ];
        }

        if (
            $allocation->getStatus() === AllocationStatus::HELD->value
            && $allocation->getExpiresAt() > $now
        ) {
            return [
                'status' => SeatStatus::HELD->value,
                'expiresAt' => $allocation->getExpiresAt(),
            ];
        }

        return [
            'status' => SeatStatus::AVAILABLE->value,
            'expiresAt' => null,
        ];
    }
}
