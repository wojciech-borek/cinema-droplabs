<?php

declare(strict_types=1);

namespace App\Mapper;

use App\DTO\Response\HallListResponse;
use App\DTO\Response\HallResponse;
use App\DTO\Response\PaginationMeta;
use App\Entity\Hall;
use App\Exception\EntityIdGenerationException;

final class HallMapper
{
    public static function mapToResponse(Hall $hall): HallResponse
    {
        $id = $hall->getId();

        if (null === $id) {
            throw new EntityIdGenerationException('Hall');
        }

        return new HallResponse(
            id: $id,
            name: $hall->getName(),
            isActive: $hall->isActive(),
            totalSeats: $hall->getTotalSeats(),
        );
    }

    /**
     * @param Hall[] $halls
     */
    public static function mapToListResponse(array $halls, int $currentPage, int $itemsPerPage, int $totalItems): HallListResponse
    {
        $data = array_map(
            static fn (Hall $hall) => self::mapToResponse($hall),
            $halls
        );

        $totalPages = (int) ceil($totalItems / $itemsPerPage);

        return new HallListResponse(
            data: $data,
            meta: new PaginationMeta(
                currentPage: $currentPage,
                itemsPerPage: $itemsPerPage,
                totalItems: $totalItems,
                totalPages: $totalPages,
            ),
        );
    }
}
