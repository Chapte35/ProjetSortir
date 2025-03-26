<?php
namespace App\Service;

use App\Entity\Participant;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class AnnulerSortieService
{
    private $entityManager;
    private $sortieRepository;
    private $etatRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository
    ) {
        $this->entityManager = $entityManager;
        $this->sortieRepository = $sortieRepository;
        $this->etatRepository = $etatRepository;
    }

    public function annulerSortie(int $sortieId, string $justification, Participant $organisateur)
    {
        // Récupérer la sortie
        $sortie = $this->sortieRepository->find($sortieId);

        if (!$sortie) {
            throw new \Exception("Sortie non trouvée");
        }

        // Ajoutez ici les logiques de vérification
        if ($sortie->getOrganisateur() !== $organisateur) {
            throw new \Exception("Vous n'avez pas la permission d'annuler cette sortie.");
        }

        // Mettre à jour la sortie
        $sortie->setInfosSortie("Sortie annulée : ". $justification);
        $sortie->setEtat($this->etatRepository->find(5));
        // Enregistrer la sortie
        $this->entityManager->persist($sortie);
        $this->entityManager->flush();

        return $sortie;
    }
}