<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Hall;
use App\Entity\Screening;
use App\Entity\Seat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BookingControllerTest extends WebTestCase
{
    /**
     * @group skip-in-ci
     */
    public function testCreateBookingSuccessfully(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $entityManager->beginTransaction();

        try {
            $hall = new Hall('Test Hall');
            $entityManager->persist($hall);

            $seat1 = new Seat($hall, 1, 1);
            $seat2 = new Seat($hall, 1, 2);
            $entityManager->persist($seat1);
            $entityManager->persist($seat2);

            $screening = new Screening(
                hall: $hall,
                movieTitle: 'Test Movie',
                startsAt: new \DateTimeImmutable('+2 hours')
            );
            $entityManager->persist($screening);

            $entityManager->flush();

            $requestData = [
                'screeningId' => $screening->getId(),
                'seatIds' => [
                    $seat1->getId(),
                    $seat2->getId(),
                ],
                'customerEmail' => 'test@example.com',
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
            $this->assertSame('test@example.com', $responseData['customerEmail']);
            $this->assertCount(2, $responseData['seats'], 'Should have 2 booked seats');
            $this->assertSame('HELD', $responseData['status'], 'Booking should be in HELD status');
            $this->assertNotNull($responseData['expiresAt'], 'Booking should have expiration time');
        } finally {
            $entityManager->rollback();
            $entityManager->close();
        }
    }
}
