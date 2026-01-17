<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Screening;
use App\Repository\HallRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ScreeningFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly HallRepository $hallRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $halls = $this->hallRepository->findBy(['isActive' => true]);

        if (empty($halls)) {
            throw new \RuntimeException('No active hall found. Run HallFixtures first.');
        }

        $hall = $halls[0];

        $screening = new Screening(
            hall: $hall,
            movieTitle: 'Inception',
            startsAt: new \DateTimeImmutable('+2 hours')
        );

        $manager->persist($screening);
        $manager->flush();

        echo "Created screening: {$screening->getMovieTitle()} at {$screening->getStartsAt()->format('Y-m-d H:i')}\n";
    }

    public function getDependencies(): array
    {
        return [
            HallFixtures::class,
        ];
    }
}
