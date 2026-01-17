<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'screenings')]
class Screening
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Hall::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Hall $hall;

    #[ORM\Column(type: 'string', length: 255)]
    private string $movieTitle;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $startsAt;

    public function __construct(
        Hall $hall,
        string $movieTitle,
        \DateTimeImmutable $startsAt
    ) {
        $this->hall = $hall;
        $this->movieTitle = $movieTitle;
        $this->startsAt = $startsAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHall(): Hall
    {
        return $this->hall;
    }

    public function getMovieTitle(): string
    {
        return $this->movieTitle;
    }

    public function getStartsAt(): \DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function hasStarted(): bool
    {
        return $this->startsAt <= new \DateTimeImmutable();
    }
}
