<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Application\Query\GetAvailableSeatsCount\GetAvailableSeatsCountQuery;
use App\Bus\Interface\QueryBusInterface;
use App\DTO\Response\PaginationMeta;
use App\DTO\Response\ScreeningListResponse;
use App\DTO\Response\ScreeningResponse;
use App\Entity\Screening;
use App\Exception\EntityIdGenerationException;

final class ScreeningMapper
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    public function mapToResponse(Screening $screening): ScreeningResponse
    {
        $id = $screening->getId();

        if (null === $id) {
            throw new EntityIdGenerationException('Screening');
        }

        $availableSeatsCountQuery = new GetAvailableSeatsCountQuery(screeningId: $id);

        /** @var int $availableSeatsCount */
        $availableSeatsCount = $this->queryBus->ask($availableSeatsCountQuery);

        return new ScreeningResponse(
            id: $id,
            hallName: $screening->getHall()->getName(),
            movieTitle: $screening->getMovieTitle()->toString(),
            startsAt: $screening->getStartsAt(),
            availableSeatsCount: $availableSeatsCount,
        );
    }

    /**
     * @param Screening[] $screenings
     */
    public function mapToListResponse(array $screenings, int $currentPage, int $itemsPerPage, int $totalItems): ScreeningListResponse
    {
        $data = array_map(
            fn (Screening $screening) => $this->mapToResponse($screening),
            $screenings
        );

        $totalPages = (int) ceil($totalItems / $itemsPerPage);

        return new ScreeningListResponse(
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
