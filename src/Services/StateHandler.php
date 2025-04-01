<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class StateHandler
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


    public function handleStates(array $sorties): void
    {
        //les états
        $etats = $this->etatRepository->findAll();




        foreach ($sorties as $sortie) {

                $dateDebut = $sortie->getDateHeureDebut(); //date de debut de sortie
                $duree = $sortie->getDuree();
                $dateFin = $sortie->getDateHeureDebut()->add($duree) ;
                $dateCloture = $sortie->getDateLimiteInscription();
                $currentDate = new DateTime(); //maintenant






                if ($dateFin < $currentDate ){
                    foreach ($etats as $etat) {
                        if ($etat->getLibelle() == 'Passée'){
                            $sortie->setEtat($etat);
                        }
                    }
                }





                if ($dateDebut < $currentDate ){
                    foreach ($etats as $etat) {
                        if ($etat->getLibelle() == 'Activite en Cours'){
                            $sortie->setEtat($etat);
                        }
                    }
                }






                if ($dateCloture < $currentDate ){
                    foreach ($etats as $etat) {
                        if ($etat->getLibelle() == 'Cloturée'){
                            $sortie->setEtat($etat);
                        }
                    }
                }





                if ($dateDebut <= $currentDate) {
                    foreach ($etats as $etat) {
                        if ($etat->getLibelle() == 'Ouverte') {
                        $sortie->setEtat($etat);
                        }

                    }
                }


                if ($dateFin <= $currentDate) {
                    $interval = $dateFin->diff($currentDate);

                    if ($interval->days >= 30) {
                        foreach ($etats as $etat) {
                            if ($etat->getLibelle() == 'Archiver') {
                                $sortie->setEtat($etat);
                            }
                        }
                    }
                }


                $this->entityManager->persist($sortie);
                $this->entityManager->flush();

        }


    }
}
