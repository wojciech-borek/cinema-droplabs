<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\AllocationStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'seat_allocations')]
#[ORM\UniqueConstraint(
    name: 'uniq_screening_seat',
    columns: ['screening_id', 'seat_id']
)]
#[ORM\Index(columns: ['status', 'expires_at'])]
class SeatAllocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Screening::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Screening $screening;

    #[ORM\ManyToOne(targetEntity: Seat::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Seat $seat;

    #[ORM\ManyToOne(targetEntity: Booking::class, inversedBy: 'allocations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Booking $booking;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $expiresAt = null;

    public function __construct(
        Screening $screening,
        Seat $seat,
        Booking $booking,
        AllocationStatus $status,
        ?\DateTimeImmutable $expiresAt = null
    ) {
        $this->screening = $screening;
        $this->seat = $seat;
        $this->booking = $booking;
        $this->status = $status->value;
        $this->expiresAt = $expiresAt;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function isAvailable(): bool
    {
        if ($this->status === AllocationStatus::CONFIRMED->value) {
            return false;
        }

        if ($this->status === AllocationStatus::HELD->value) {
            return null !== $this->expiresAt && $this->expiresAt < new \DateTimeImmutable();
        }

        return true;
    }

    public function confirm(): void
    {
        $this->status = AllocationStatus::CONFIRMED->value;
        $this->expiresAt = null;
    }

    public function cancel(): void
    {
        $this->status = AllocationStatus::CANCELLED->value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScreening(): Screening
    {
        return $this->screening;
    }

    public function getSeat(): Seat
    {
        return $this->seat;
    }

    public function getBooking(): Booking
    {
        return $this->booking;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }
}
