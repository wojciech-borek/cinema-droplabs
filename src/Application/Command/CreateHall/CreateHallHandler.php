<?php

declare(strict_types=1);

namespace App\Application\Command\CreateHall;

use App\Entity\Hall;
use App\Exception\EntityIdGenerationException;
use App\Repository\HallRepository;
use App\Service\SeatGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command_bus')]
final readonly class CreateHallHandler
{
    public function __construct(
        private HallRepository $hallRepository,
        private SeatGeneratorService $seatGenerator,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(CreateHallCommand $command): int
    {
        $hall = new Hall(name: $command->name);

        $this->seatGenerator->generate(
            hall: $hall,
            rowsCount: $command->rowsCount,
            seatsPerRow: $command->seatsPerRow
        );

        $this->hallRepository->save($hall);

        $this->entityManager->flush();

        return $hall->getId() ?? throw new EntityIdGenerationException('Hall');
    }
}
