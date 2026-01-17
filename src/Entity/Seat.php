<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'seats')]
#[ORM\UniqueConstraint(name: 'uniq_hall_row_seat', columns: ['hall_id', 'row_no', 'seat_no'])]
class Seat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Hall::class, inversedBy: 'seats')]
    #[ORM\JoinColumn(name: 'hall_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Hall $hall;

    #[ORM\Column(name: 'row_no', type: 'integer')]
    private int $rowNumber;

    #[ORM\Column(name: 'seat_no', type: 'integer')]
    private int $seatNumber;

    public function __construct(Hall $hall, int $rowNumber, int $seatNumber)
    {
        if ($rowNumber <= 0) {
            throw new \InvalidArgumentException('Row number must be positive.');
        }

        if ($seatNumber <= 0) {
            throw new \InvalidArgumentException('Seat number must be positive.');
        }

        $this->hall = $hall;
        $this->rowNumber = $rowNumber;
        $this->seatNumber = $seatNumber;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHall(): Hall
    {
        return $this->hall;
    }

    public function getRowNumber(): int
    {
        return $this->rowNumber;
    }

    public function getSeatNumber(): int
    {
        return $this->seatNumber;
    }
}
