<?php

declare(strict_types=1);

namespace App\Controller\Public;

use App\Application\Command\CreateBooking\CreateBookingCommand;
use App\Application\Query\GetBooking\GetBookingQuery;
use App\Bus\Interface\CommandBusInterface;
use App\Bus\Interface\QueryBusInterface;
use App\DTO\Request\CreateBookingRequest;
use App\DTO\Response\BookingResponse;
use App\Exception\EntityIdGenerationException;
use App\ValueObject\EmailAddress;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/bookings', name: 'api_v1_bookings_')]
final class BookingController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload]
        CreateBookingRequest $request
    ): JsonResponse {
        $command = new CreateBookingCommand(
            screeningId: $request->screeningId,
            seatIds: $request->seatIds,
            customerEmail: EmailAddress::fromString($request->customerEmail),
        );

        $bookingId = $this->commandBus->dispatch($command);

        if (!is_int($bookingId)) {
            throw new EntityIdGenerationException('Booking');
        }

        $query = new GetBookingQuery(bookingId: $bookingId);

        /** @var BookingResponse $response */
        $response = $this->queryBus->ask($query);

        return $this->json(data: $response, status: Response::HTTP_CREATED);
    }
}
