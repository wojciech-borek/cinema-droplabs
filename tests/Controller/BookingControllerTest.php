<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Booking;
use App\Entity\Hall;
use App\Entity\Screening;
use App\Entity\Seat;
use App\Entity\SeatAllocation;
use App\Enum\AllocationStatus;
use App\ValueObject\EmailAddress;
use App\ValueObject\MovieTitle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BookingControllerTest extends WebTestCase
{
    /**
     * @dataProvider createBookingDataProvider
     *
     * @group skip-in-ci
     *
     * @param array<int> $seatIds
     */
    public function testCreateBookingSuccessfully(array $seatIds, string $customerEmail, int $expectedSeatCount): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $entityManager->beginTransaction();

        try {
            $hall = new Hall('Test Hall');
            $entityManager->persist($hall);

            $seats = [];

            for ($i = 1; $i <= 5; ++$i) {
                $seat = new Seat($hall, 1, $i);
                $entityManager->persist($seat);
                $seats[] = $seat;
            }

            $screening = new Screening(
                hall: $hall,
                movieTitle: MovieTitle::fromString('Test Movie'),
                startsAt: new \DateTimeImmutable('+2 hours')
            );
            $entityManager->persist($screening);
            $entityManager->flush();

            $actualSeatIds = array_map(fn ($index) => $seats[$index - 1]->getId(), $seatIds);

            $requestData = [
                'screeningId' => $screening->getId(),
                'seatIds' => $actualSeatIds,
                'customerEmail' => $customerEmail,
            ];

            $jsonContent = json_encode($requestData);
            $this->assertNotFalse($jsonContent, 'Request data should be valid JSON');

            $client->request(
                method: 'POST',
                uri: '/api/v1/bookings',
                server: ['CONTENT_TYPE' => 'application/json'],
                content: $jsonContent
            );

            $this->assertResponseStatusCodeSame(201, 'Expected 201 Created status');

            $this->assertResponseHeaderSame('content-type', 'application/json');

            $content = $client->getResponse()->getContent();
            $this->assertNotFalse($content, 'Response content should not be empty');
            $responseData = json_decode($content, true);

            $this->assertIsArray($responseData);
            $this->assertArrayHasKey('id', $responseData);
            $this->assertArrayHasKey('screeningId', $responseData);
            $this->assertArrayHasKey('customerEmail', $responseData);
            $this->assertArrayHasKey('seats', $responseData);
            $this->assertArrayHasKey('status', $responseData);
            $this->assertArrayHasKey('expiresAt', $responseData);

            $this->assertIsInt($responseData['id']);
            $this->assertSame($screening->getId(), $responseData['screeningId']);
            $this->assertSame($customerEmail, $responseData['customerEmail']);
            $this->assertCount($expectedSeatCount, $responseData['seats'], "Should have {$expectedSeatCount} booked seats");
            $this->assertSame('HELD', $responseData['status'], 'Booking should be in HELD status');
            $this->assertNotNull($responseData['expiresAt'], 'Booking should have expiration time');
        } finally {
            $entityManager->rollback();
            $entityManager->close();
        }
    }

    /**
     * @dataProvider createBookingValidationDataProvider
     *
     * @group skip-in-ci
     *
     * @param array<string, mixed> $requestData
     */
    public function testCreateBookingValidationFails(array $requestData, int $expectedStatusCode, ?string $expectedErrorMessage = null): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $entityManager->beginTransaction();

        try {
            $hall = new Hall('Test Hall');
            $entityManager->persist($hall);

            $seats = [];
            for ($i = 1; $i <= 3; ++$i) {
                $seat = new Seat($hall, 1, $i);
                $entityManager->persist($seat);
                $seats[] = $seat;
            }

            $screening = new Screening(
                hall: $hall,
                movieTitle: MovieTitle::fromString('Test Movie'),
                startsAt: new \DateTimeImmutable('+2 hours')
            );
            $entityManager->persist($screening);
            $entityManager->flush();

            if (isset($requestData['createOccupiedBooking']) && $requestData['createOccupiedBooking']) {
                $booking = new Booking(
                    screening: $screening,
                    customerEmail: EmailAddress::fromString('existing@test.com'),
                    expiresAt: new \DateTimeImmutable('+30 minutes')
                );
                $entityManager->persist($booking);

                $seatAllocation = new SeatAllocation(
                    $screening,
                    $seats[0],
                    $booking,
                    AllocationStatus::HELD,
                    new \DateTimeImmutable('+30 minutes')
                );
                $entityManager->persist($seatAllocation);
                $entityManager->flush();
            }

            $jsonContent = json_encode($requestData['data']);
            $this->assertNotFalse($jsonContent, 'Request data should be valid JSON');

            $client->request(
                method: 'POST',
                uri: '/api/v1/bookings',
                server: ['CONTENT_TYPE' => 'application/json'],
                content: $jsonContent
            );

            $this->assertResponseStatusCodeSame($expectedStatusCode);

            if ($expectedErrorMessage) {
                $content = $client->getResponse()->getContent();
                $this->assertNotFalse($content);
                $responseData = json_decode($content, true);
                if (!is_array($responseData)) {
                    $this->fail('Response data should be an array');
                }

                /** @psalm-var array<string, mixed> $responseData */
                if (isset($responseData['error']['details']['violations'])) { // @phpstan-ignore-line
                    $violationsText = json_encode($responseData['error']['details']['violations']);
                    $this->assertIsString($violationsText);
                    $this->assertStringContainsString($expectedErrorMessage, $violationsText, 'Validation violations should contain expected text');
                } else {
                    $message = $responseData['message'] ?? $responseData['error']['message'] ?? ''; // @phpstan-ignore-line
                    $this->assertIsString($message);
                    $this->assertStringContainsString($expectedErrorMessage, $message, 'Error message should contain expected text');
                }
            }
        } finally {
            $entityManager->rollback();
            $entityManager->close();
        }
    }

    /**
     * @return array<array<int|string|array<int>>>
     */
    public static function createBookingDataProvider(): array
    {
        return [
            'single seat booking' => [
                [1],
                'single@test.com',
                1,
            ],
            'two seats booking' => [
                [1, 2],
                'two@test.com',
                2,
            ],
            'three seats booking' => [
                [1, 2, 3],
                'three@test.com',
                3,
            ],
            'booking with different email format' => [
                [2, 3],
                'user.name+tag@example-domain.co.uk',
                2,
            ],
        ];
    }

    /**
     * @return array<array<int|string|array<string, mixed>>>
     */
    public static function createBookingValidationDataProvider(): array
    {
        return [
            'invalid screening id' => [
                [
                    'data' => [
                        'screeningId' => 99999,
                        'seatIds' => [1],
                        'customerEmail' => 'test@example.com',
                    ],
                ],
                404,
                'was not found',
            ],
            'invalid email format' => [
                [
                    'data' => [
                        'screeningId' => 1,
                        'seatIds' => [1],
                        'customerEmail' => 'invalid-email',
                    ],
                ],
                422,
                'valid email address',
            ],
            'empty seat ids' => [
                [
                    'data' => [
                        'screeningId' => 1,
                        'seatIds' => [],
                        'customerEmail' => 'test@example.com',
                    ],
                ],
                422,
                'At least 1 seat',
            ],
            'missing screening id' => [
                [
                    'data' => [
                        'seatIds' => [1],
                        'customerEmail' => 'test@example.com',
                    ],
                ],
                422,
                'screeningId',
            ],
            'missing customer email' => [
                [
                    'data' => [
                        'screeningId' => 1,
                        'seatIds' => [1],
                    ],
                ],
                422,
                'customerEmail',
            ],
        ];
    }
}
