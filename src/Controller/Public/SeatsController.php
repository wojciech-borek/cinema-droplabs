<?php

declare(strict_types=1);

namespace App\Controller\Public;

use App\Application\Query\GetAvailableSeats\GetAvailableSeatsQuery;
use App\Bus\Interface\QueryBusInterface;
use App\DTO\Response\AvailableSeatsResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/screenings', name: 'api_v1_screenings_')]
final class SeatsController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    #[Route('/{id}/seats', name: 'seats', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getAvailableSeats(int $id): JsonResponse
    {
        $query = new GetAvailableSeatsQuery(screeningId: $id);

        /** @var AvailableSeatsResponse $response */
        $response = $this->queryBus->ask($query);

        return $this->json(data: $response, status: Response::HTTP_OK);
    }
}
