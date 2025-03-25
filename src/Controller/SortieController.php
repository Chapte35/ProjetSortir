<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortiesType;
use App\Repository\EtatRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sortie', name: 'sortie_')]

final class SortieController extends AbstractController
{
    #[Route('/creer', name: 'creer')]
    public function creer(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortiesType::class, $sortie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $_POST['action'] == 'publier'){

            $sortie->setDuree(DateInterval::createFromDateString($form->get('dureeMinutes')->getData()." min"));
            $sortie ->setEstPublie(true);
            $sortie ->setOrganisateur($this->getUser());
            $sortie -> setEtat($etatRepository->find(1));

            $entityManager -> persist($sortie);


            $entityManager ->flush();

        }

        return $this->render('sortie/creer.html.twig', [
            'controller_name' => 'SortieController',
            'form' => $form
        ]);
    }
}
