<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $creee = new Etat();
        $creee->setLibelle("Créée");
        $manager->persist($creee);

        $ouverte = new Etat();
        $ouverte->setLibelle("Ouverte");
        $manager->persist($ouverte);

        $cloturee = new Etat();
        $cloturee->setLibelle("Cloturée");
        $manager->persist($cloturee);

        $enCours = new Etat();
        $enCours->setLibelle("Activite en Cours");
        $manager->persist($enCours);

        $passee = new Etat();
        $passee->setLibelle("Passée");
        $manager->persist($passee);

        $annulee = new Etat();
        $annulee->setLibelle("Annulée");
        $manager->persist($annulee);

        $annulee = new Etat();
        $annulee->setLibelle("Archiver");
        $manager->persist($annulee);

        $manager->flush();

    }
}
