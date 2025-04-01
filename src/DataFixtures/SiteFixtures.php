<?php

namespace App\DataFixtures;

use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SiteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $site = new Site();
        $site->setNom("Chartres De Bretagne ");
        $manager->persist($site);

        $site1 = new Site();
        $site1->setNom("Niort ");
        $manager->persist($site1);

        $site2 = new Site();
        $site2->setNom("Nantes ");
        $manager->persist($site2);



        $manager->flush();
    }
}
