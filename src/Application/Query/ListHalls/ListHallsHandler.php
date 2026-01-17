<?php

declare(strict_types=1);

namespace App\Application\Query\ListHalls;

use App\DTO\Response\HallListResponse;
use App\Mapper\HallMapper;
use App\Repository\HallRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query_bus')]
final readonly class ListHallsHandler
{
    public function __construct(
        private HallRepository $hallRepository,
    ) {
    }

    public function __invoke(ListHallsQuery $query): HallListResponse
    {
        $result = $this->hallRepository->findActivePaginated(
            page: $query->page,
            limit: $query->limit
        );

        return HallMapper::mapToListResponse(
            halls: $result['data'],
            currentPage: $query->page,
            itemsPerPage: $query->limit,
            totalItems: $result['total']
        );
    }
}
