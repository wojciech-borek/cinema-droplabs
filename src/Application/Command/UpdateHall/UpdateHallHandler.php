<?php

declare(strict_types=1);

namespace App\Application\Command\UpdateHall;

use App\Entity\Hall;
use App\Exception\EntityNotFoundException;
use App\Repository\HallRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command_bus')]
final readonly class UpdateHallHandler
{
    public function __construct(
        private HallRepository $hallRepository,
    ) {
    }

    public function __invoke(UpdateHallCommand $command): void
    {
        $hall = $this->getActiveHall($command->id);

        $hall->rename($command->name);

        $this->hallRepository->save($hall);
    }

    private function getActiveHall(int $id): Hall
    {
        $hall = $this->hallRepository->findActiveById($id);

        if (null === $hall) {
            throw new EntityNotFoundException('Hall', $id);
        }

        return $hall;
    }
}
