<?php

namespace App\DataFixtures;

use App\Entity\GroupePrive;
use App\Repository\ParticipantRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GroupePriveFixture extends Fixture implements DependentFixtureInterface
{
    private ParticipantRepository $participantRepository;

    /**
     * @param ParticipantRepository $participantRepository
     */
    public function __construct(ParticipantRepository $participantRepository)
    {
        $this->participantRepository = $participantRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $participants = $this->participantRepository->findAll();


        for ($i = 0; $i < 60; $i++) {

            $groupe = new GroupePrive();
            $groupe->setNom($faker->word." ".$faker->colorName." ".$faker->emoji);
            $groupe->setProprio($faker->randomElement($participants));
            for ($j = 0; $j < $faker->randomDigit(); $j++) {
                $groupe->addMembre($faker->randomElement($participants));
            }
            $manager->persist($groupe);
        }

        $manager->flush();
    }


    public function getDependencies(): array
    {
        return [
            ParticipantFixtures::class,
        ];
    }
}
