<?php

declare(strict_types=1);

namespace App\Repository\Interface;

use App\Entity\Screening;

interface ScreeningRepositoryInterface
{
    public function findOneByIdWithLock(int $id): ?Screening;

    public function save(Screening $screening): void;

    public function findById(int $id): ?Screening;

    /**
     * @return array{data: array<Screening>, total: int}
     */
    public function findAllPaginated(int $page, int $limit): array;
}
