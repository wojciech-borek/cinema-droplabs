<?php

declare(strict_types=1);

namespace App\Exception;

class EntityNotFoundException extends \RuntimeException implements ApplicationException
{
    public function __construct(
        private readonly string $entityType,
        private readonly int $entityId,
    ) {
        parent::__construct(
            sprintf('%s with ID %d was not found', $this->entityType, $this->entityId)
        );
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }
}
