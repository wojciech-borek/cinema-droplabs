<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\AllocationStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'bookings')]
#[ORM\Index(columns: ['customer_email'])]
#[ORM\Index(columns: ['expires_at'])]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Screening::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Screening $screening;

    #[ORM\Column(type: 'string', length: 255)]
    private string $customerEmail;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status;

    /**
     * @var Collection<int, SeatAllocation>
     */
    #[ORM\OneToMany(
        mappedBy: 'booking',
        targetEntity: SeatAllocation::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Collection $allocations;

    public function __construct(
        Screening $screening,
        string $customerEmail,
        \DateTimeImmutable $expiresAt
    ) {
        $this->screening = $screening;
        $this->customerEmail = $customerEmail;
        $this->expiresAt = $expiresAt;
        $this->createdAt = new \DateTimeImmutable();
        $this->status = AllocationStatus::HELD->value;
        $this->allocations = new ArrayCollection();
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function canBeConfirmed(): bool
    {
        return $this->status === AllocationStatus::HELD->value && !$this->isExpired();
    }

    public function confirm(): void
    {
        if (!$this->canBeConfirmed()) {
            throw new \DomainException('Cannot confirm this booking');
        }

        $this->status = AllocationStatus::CONFIRMED->value;

        foreach ($this->allocations as $allocation) {
            $allocation->confirm();
        }
    }

    public function cancel(): void
    {
        if ($this->status === AllocationStatus::CANCELLED->value) {
            return;
        }

        $this->status = AllocationStatus::CANCELLED->value;

        foreach ($this->allocations as $allocation) {
            $allocation->cancel();
        }
    }

    public function addAllocation(SeatAllocation $allocation): void
    {
        if ($this->allocations->contains($allocation)) {
            return;
        }

        $this->allocations->add($allocation);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScreening(): Screening
    {
        return $this->screening;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return Collection<int, SeatAllocation>
     */
    public function getAllocations(): Collection
    {
        return $this->allocations;
    }

    public function getTotalSeats(): int
    {
        return $this->allocations->count();
    }
}
