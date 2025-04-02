<?php

namespace App\DataFixtures;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\VilleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SortieFixtures extends Fixture  implements DependentFixtureInterface
{
    private SiteRepository $siteRepository;
    private ParticipantRepository $participantRepository;
    private EtatRepository $etatRepository;
    private LieuRepository $lieuRepository;
    private VilleRepository $villeRepository;

    /**
     * @param SiteRepository $siteRepository
     * @param ParticipantRepository $participantRepository
     * @param EtatRepository $etatRepository
     * @param LieuRepository $lieuRepository
     * @param VilleRepository $villeRepository
     */
    public function __construct(SiteRepository $siteRepository, ParticipantRepository $participantRepository, EtatRepository $etatRepository, LieuRepository $lieuRepository, VilleRepository $villeRepository)
    {
        $this->siteRepository = $siteRepository;
        $this->participantRepository = $participantRepository;
        $this->etatRepository = $etatRepository;
        $this->lieuRepository = $lieuRepository;
        $this->villeRepository = $villeRepository;
    }


    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $sites = $this->siteRepository->findAll();
        $etats = $this->etatRepository->findAll();
        $lieux = $this->lieuRepository->findAll();
        $participants = $this->participantRepository->findAll();

        for ($i = 0; $i < 40; $i++) {

        $sortie = new Sortie();



        $sortie->setNom($faker->city. " ". $faker->company);
        $sortie->setSite($faker->randomElement($sites));
        $sortie->setInfosSortie($faker->realText(20,5));
        $sortie->setEtat($faker->randomElement($etats));
        $dateTime = $faker->dateTimeBetween('-2 months','+4 months',null);
        $sortie->setDateHeureDebut(\DateTimeImmutable::createFromMutable($dateTime));
        $dateTime = $dateTime->add(\DateInterval::createFromDateString('10 days'));
        $sortie->setDateLimiteInscription(\DateTimeImmutable::createFromMutable($dateTime));
        $sortie->setLieu($faker->randomElement($lieux));
        $sortie->setNbInscriptionsMax($faker->numberBetween(3,20));
        $proprio = $faker->randomElement($participants);
        $sortie->setOrganisateur($proprio);
        $sortie->setEstPublie($faker->boolean);
        if ($sortie->isEstPublie()){
        $sortie->addParticipant($proprio);
        }
        $manager->persist($sortie);

        }

        $manager->flush();
    }


    public function getDependencies(): array
    {
        return [
            SiteFixtures::class,
            ParticipantFixtures::class,
            EtatFixtures::class,
            LieuFixtures::class,
        ];
    }
}
