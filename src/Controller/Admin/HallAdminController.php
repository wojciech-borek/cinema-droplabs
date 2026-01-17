<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Application\Command\CreateHall\CreateHallCommand;
use App\Application\Command\DeleteHall\DeleteHallCommand;
use App\Application\Command\UpdateHall\UpdateHallCommand;
use App\Application\Query\GetHall\GetHallQuery;
use App\Application\Query\ListHalls\ListHallsQuery;
use App\Bus\Interface\CommandBusInterface;
use App\Bus\Interface\QueryBusInterface;
use App\DTO\Request\HallCreateRequest;
use App\DTO\Request\HallUpdateRequest;
use App\DTO\Request\PaginationRequest;
use App\DTO\Response\HallListResponse;
use App\DTO\Response\HallResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/admin/halls', name: 'api_v1_admin_halls_')]
final class HallAdminController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload]
        HallCreateRequest $request
    ): JsonResponse {
        $command = new CreateHallCommand(
            name: $request->name,
            rowsCount: $request->rowsCount,
            seatsPerRow: $request->seatsPerRow,
        );

        /** @var int $hallId */
        $hallId = $this->commandBus->dispatch($command);

        $query = new GetHallQuery(id: $hallId);

        /** @var HallResponse $response */
        $response = $this->queryBus->ask($query);

        return $this->json(data: $response, status: Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        int $id,
        #[MapRequestPayload]
        HallUpdateRequest $request
    ): JsonResponse {
        $command = new UpdateHallCommand(
            id: $id,
            name: $request->name,
        );

        $this->commandBus->dispatch($command);

        $query = new GetHallQuery(id: $id);

        /** @var HallResponse $response */
        $response = $this->queryBus->ask($query);

        return $this->json(data: $response, status: Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): Response
    {
        $command = new DeleteHallCommand(id: $id);

        $this->commandBus->dispatch($command);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(
        #[MapQueryString(validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
        PaginationRequest $pagination
    ): JsonResponse {
        $query = new ListHallsQuery(
            page: $pagination->page,
            limit: $pagination->limit,
        );

        /** @var HallListResponse $response */
        $response = $this->queryBus->ask($query);

        return $this->json(data: $response, status: Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $query = new GetHallQuery(id: $id);

        /** @var HallResponse $response */
        $response = $this->queryBus->ask($query);

        return $this->json(data: $response, status: Response::HTTP_OK);
    }
}
