<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class Archiver
{

    private SortieRepository $sortieRepository;
    private EtatRepository $etatRepository;
    private EntityManagerInterface $entityManager;


    public function __construct(SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager)
    {
        $this->sortieRepository = $sortieRepository;
        $this->etatRepository = $etatRepository;
        $this->entityManager = $entityManager;
    }

    public function archiver(array $sorties): void
    {

        $etat = $this->etatRepository->find(2);



        foreach ($sorties as $sortie) {
            $dateDebut = $sortie->getDateHeureDebut();
            $currentDate = new DateTime();
            $interval = $dateDebut->diff($currentDate);


            if ($interval->days >= 30) {
                $sortie->setEtat($etat);
                $this->entityManager->persist($sortie);
                $this->entityManager->flush();
            }

        }
    }
}