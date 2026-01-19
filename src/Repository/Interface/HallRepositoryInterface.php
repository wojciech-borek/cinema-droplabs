<?php

declare(strict_types=1);

namespace App\Repository\Interface;

use App\Entity\Hall;

interface HallRepositoryInterface
{
    public function save(Hall $hall): void;

    public function findActiveById(int $id): ?Hall;

    /**
     * @return array{data: array<Hall>, total: int}
     */
    public function findActivePaginated(int $page, int $limit): array;
}
