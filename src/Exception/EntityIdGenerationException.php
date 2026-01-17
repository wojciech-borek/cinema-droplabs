<?php

declare(strict_types=1);

namespace App\Exception;

final class EntityIdGenerationException extends \RuntimeException implements ApplicationException
{
    public function __construct(
        private readonly string $entityType,
    ) {
        parent::__construct(
            sprintf('Failed to generate ID for new %s entity', $this->entityType)
        );
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }
}
