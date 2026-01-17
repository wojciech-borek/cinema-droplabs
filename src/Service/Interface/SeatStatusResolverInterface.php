<?php

declare(strict_types=1);

namespace App\Service\Interface;

use App\Entity\SeatAllocation;

interface SeatStatusResolverInterface
{
    /**
     * @return array{status: string, expiresAt: ?\DateTimeImmutable}
     */
    public function resolveStatus(?SeatAllocation $allocation, \DateTimeImmutable $now): array;
}
