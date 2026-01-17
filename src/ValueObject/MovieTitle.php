<?php

declare(strict_types=1);

namespace App\ValueObject;

final readonly class MovieTitle implements \Stringable
{
    private function __construct(
        private string $value
    ) {
        $trimmedValue = trim($value);
        if (empty($trimmedValue)) {
            throw new \InvalidArgumentException('Movie title cannot be empty.');
        }

        if (mb_strlen($trimmedValue) > 255) {
            throw new \InvalidArgumentException('Movie title cannot be longer than 255 characters.');
        }
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
