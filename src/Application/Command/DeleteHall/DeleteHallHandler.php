<?php

declare(strict_types=1);

namespace App\Application\Command\DeleteHall;

use App\Entity\Hall;
use App\Exception\EntityNotFoundException;
use App\Repository\Interface\HallRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command_bus')]
final readonly class DeleteHallHandler
{
    public function __construct(
        private HallRepositoryInterface $hallRepository,
    ) {
    }

    public function __invoke(DeleteHallCommand $command): void
    {
        $hall = $this->getActiveHall($command->id);

        $hall->deactivate();

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
