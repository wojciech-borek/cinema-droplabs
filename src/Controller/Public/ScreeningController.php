<?php

declare(strict_types=1);

namespace App\Controller\Public;

use App\Application\Query\ListScreenings\ListScreeningsQuery;
use App\Bus\Interface\QueryBusInterface;
use App\DTO\Request\PaginationRequest;
use App\DTO\Response\ScreeningListResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/screenings', name: 'api_v1_screenings_')]
final class ScreeningController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(
        #[MapQueryString(validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
        PaginationRequest $pagination
    ): JsonResponse {
        $query = new ListScreeningsQuery(
            page: $pagination->page,
            limit: $pagination->limit,
        );

        /** @var ScreeningListResponse $response */
        $response = $this->queryBus->ask($query);

        return $this->json(data: $response, status: Response::HTTP_OK);
    }
}
