<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Repository\VilleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LieuFixtures extends Fixture  implements DependentFixtureInterface
{
    private VilleRepository $villeRepository;

    /**
     * @param VilleRepository $villeRepository
     */
    public function __construct(VilleRepository $villeRepository)
    {
        $this->villeRepository = $villeRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $villes = $this->villeRepository->findAll();

        for ($i = 0; $i < 25; $i++) {
            $lieu = new Lieu();
            $lieu->setNom($faker->address);
            $lieu->setVille($faker->randomElement($villes));
            $lieu->setRue($faker->streetName);
            $lieu->setLatitude($faker->latitude);
            $lieu->setLongitude($faker->longitude);
            $manager->persist($lieu);
        }

        $manager->flush();
    }


    public function getDependencies(): array
    {
        return [
            VilleFixtures::class,
        ];
    }
}
