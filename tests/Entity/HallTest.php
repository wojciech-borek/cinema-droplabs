<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Hall;
use App\Entity\Seat;
use PHPUnit\Framework\TestCase;

final class HallTest extends TestCase
{
    public function testCanAddSeatsToHall(): void
    {
        $hall = new Hall('Test Hall');
        $seat1 = new Seat($hall, 1, 1);
        $seat2 = new Seat($hall, 1, 2);

        $hall->addSeat($seat1);
        $hall->addSeat($seat2);

        $this->assertCount(2, $hall->getSeats());
    }

    public function testInvalidRowNumberThrowsException(): void
    {
        $hall = new Hall('Test Hall');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Row number must be positive.');

        new Seat($hall, 0, 1);
    }

    public function testInvalidSeatNumberThrowsException(): void
    {
        $hall = new Hall('Test Hall');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Seat number must be positive.');

        new Seat($hall, 1, -1);
    }
}
