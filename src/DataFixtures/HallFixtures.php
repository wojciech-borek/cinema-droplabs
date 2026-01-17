<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Hall;
use App\Service\SeatGeneratorService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HallFixtures extends Fixture
{
    public function __construct(
        private readonly SeatGeneratorService $seatGenerator
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $hall = new Hall('Hall A');

        $this->seatGenerator->generate($hall, 5, 10);

        $manager->persist($hall);
        $manager->flush();

        echo "Created hall: {$hall->getName()} (ID: {$hall->getId()}) with {$hall->getTotalSeats()} seats\n";
    }
}
