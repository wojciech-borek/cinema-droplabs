<?php

declare(strict_types=1);

namespace App\Application\Query\ListScreenings;

use App\DTO\Response\ScreeningListResponse;
use App\Mapper\ScreeningMapper;
use App\Repository\ScreeningRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query_bus')]
final readonly class ListScreeningsHandler
{
    public function __construct(
        private ScreeningRepository $screeningRepository,
        private ScreeningMapper $screeningMapper,
    ) {
    }

    public function __invoke(ListScreeningsQuery $query): ScreeningListResponse
    {
        $result = $this->screeningRepository->findAllPaginated(
            page: $query->page,
            limit: $query->limit
        );

        return $this->screeningMapper->mapToListResponse(
            screenings: $result['data'],
            currentPage: $query->page,
            itemsPerPage: $query->limit,
            totalItems: $result['total']
        );
    }
}
