<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Repository\SiteRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantFixtures extends Fixture  implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private SiteRepository $siteRepository;

    /**
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param SiteRepository $siteRepository
     */
    public function __construct(UserPasswordHasherInterface $userPasswordHasher, SiteRepository $siteRepository)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->siteRepository = $siteRepository;
    }


    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $sites = $this->siteRepository->findAll();

        $chapte = new Participant();
        $chapte->setPseudo("Chapte");
        $chapte->setNom("Le Gogol");
        $chapte->setPrenom("Chapte");
        $chapte->setPassword($this->userPasswordHasher->hashPassword($chapte,"azeaze"));
        $chapte->setActif(true);
        $chapte->setEmail("chapte@mail.fr");
        $chapte->setRoles(["ROLE_USER"]);
        $chapte->setTelephone("0636303630");
        $chapte->setSite($faker->randomElement($sites));
        $manager->persist($chapte);

        $oussama = new Participant();
        $oussama->setPseudo("Someone");
        $oussama->setNom("One");
        $oussama->setPrenom("Some");
        $oussama->setPassword($this->userPasswordHasher->hashPassword($oussama,"aaa"));
        $oussama->setActif(true);
        $oussama->setEmail("bombaclat@mail.com");
        $oussama->setRoles(["ROLE_USER"]);
        $oussama->setTelephone("0636303630");
        $oussama->setSite($faker->randomElement($sites));
        $manager->persist($oussama);

        $jeanne = new Participant();
        $jeanne->setPseudo("Thierry35");
        $jeanne->setNom("Le Dino");
        $jeanne->setPrenom("Thierry");
        $jeanne->setPassword($this->userPasswordHasher->hashPassword($jeanne,"Azerty123456!"));
        $jeanne->setActif(true);
        $jeanne->setEmail("T.ledino@gmail.com");
        $jeanne->setRoles(["ROLE_USER"]);
        $jeanne->setTelephone("0636303630");
        $jeanne->setSite($faker->randomElement($sites));
        $manager->persist($jeanne);

        $audrey = new Participant();
        $audrey->setPseudo("AudreyP");
        $audrey->setNom("Pehuet");
        $audrey->setPrenom("Audrey");
        $audrey->setPassword($this->userPasswordHasher->hashPassword($audrey,"azerty"));
        $audrey->setActif(true);
        $audrey->setEmail("audrey@mail.fr");
        $audrey->setRoles(["ROLE_USER"]);
        $audrey->setTelephone("0636303630");
        $audrey->setSite($faker->randomElement($sites));
        $manager->persist($audrey);

        $admin = new Participant();
        $admin->setPseudo("SuperAdmin");
        $admin->setNom("admin");
        $admin->setPrenom("admin");
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin,"admin"));
        $admin->setActif(true);
        $admin->setEmail("admin@admin.admin");
        $admin->setRoles(["ROLE_USER"]);
        $admin->setTelephone("0636303630");
        $admin->setSite($faker->randomElement($sites));
        $manager->persist($admin);

        for ($i = 0; $i < 10; $i++) {
            $user = new Participant();
            $user->setPseudo($faker->userName);
            $user->setNom($faker->title . " " .$faker->name);
            $user->setPrenom($faker->firstName);
            $user->setPassword($this->userPasswordHasher->hashPassword($user,"Pa$$\w0rdd"));
            $user->setActif(true);
            $user->setEmail($faker->email);
            $user->setRoles(["ROLE_USER"]);
            $user->setTelephone($faker->phoneNumber);
            $user->setSite($faker->randomElement($sites));

            $manager->persist($user);
        }


        $manager->flush();
    }


    public function getDependencies(): array
    {
        return [
            SiteFixtures::class,
        ];
    }
}
