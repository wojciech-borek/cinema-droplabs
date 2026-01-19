<?php

declare(strict_types=1);

namespace App\Application\Query\GetHall;

use App\DTO\Response\HallResponse;
use App\Exception\EntityNotFoundException;
use App\Mapper\HallMapper;
use App\Repository\Interface\HallRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query_bus')]
final readonly class GetHallHandler
{
    public function __construct(
        private HallRepositoryInterface $hallRepository,
        private HallMapper $hallMapper,
    ) {
    }

    public function __invoke(GetHallQuery $query): HallResponse
    {
        $hall = $this->hallRepository->findActiveById($query->id);

        if (null === $hall) {
            throw new EntityNotFoundException('Hall', $query->id);
        }

        return $this->hallMapper->mapToResponse($hall);
    }
}
