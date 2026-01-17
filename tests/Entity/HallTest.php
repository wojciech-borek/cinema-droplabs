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

    /**
     * @dataProvider invalidSeatDataProvider
     */
    public function testInvalidSeatCreationThrowsException(int $row, int $seatNumber, string $expectedMessage): void
    {
        $hall = new Hall('Test Hall');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        new Seat($hall, $row, $seatNumber);
    }

    /**
     * @return array<array<int|string>>
     */
    public static function invalidSeatDataProvider(): array
    {
        return [
            'negative row number' => [
                -1,
                1,
                'Row number must be positive.',
            ],
            'zero row number' => [
                0,
                1,
                'Row number must be positive.',
            ],
            'negative seat number' => [
                1,
                -1,
                'Seat number must be positive.',
            ],
            'zero seat number' => [
                1,
                0,
                'Seat number must be positive.',
            ],
        ];
    }
}
