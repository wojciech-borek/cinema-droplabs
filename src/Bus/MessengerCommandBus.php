<?php

declare(strict_types=1);

namespace App\Bus;

use App\Bus\Interface\CommandBusInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class MessengerCommandBus implements CommandBusInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    public function dispatch(object $command): mixed
    {
        $envelope = $this->commandBus->dispatch($command);

        return $envelope->last(HandledStamp::class)?->getResult();
    }
}
