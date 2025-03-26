<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\JustificationFormType;
use App\Form\SortiesType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Service\AnnulerSortieService;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sortie', name: 'sortie_')]

final class SortieController extends AbstractController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/creer', name: 'creer')]
    public function creer(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortiesType::class, $sortie);

        $form->handleRequest($request);

        if ($this->getUser()){
            $user = $this->getUser();


        if ($form->isSubmitted() && $form->isValid()  && $this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){


            $sortie->setDuree(DateInterval::createFromDateString($form->get('dureeMinutes')->getData()." min"));
            $sortie ->setOrganisateur($this->getUser());
            $sortie -> setEtat($etatRepository->find(1));
            $sortie ->setEstPublie($_POST['action'] == 'publier');

            $entityManager -> persist($sortie);
            $entityManager ->flush();

            $this->addFlash("success","La sortie : " . $sortie->getNom() . " à bien été publiée !");

            return $this->redirectToRoute('app_main');

        }}else{
            $form->addError(new FormError("Vous devez être connecté pour publier cette sortie"));
        }

        return $this->render('sortie/creer.html.twig', [
            'controller_name' => 'SortieController',
            'form' => $form
        ]);
    }

    #[Route('/modifier/{id}', name: 'modifier')]
    public function update(Sortie $sortie, Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $form = $this->createForm(SortiesType::class, $sortie);

        $form->handleRequest($request);

        //Remplir le champ qui est pas mappé
        if (!$form->isSubmitted()) {
        $form->get('dureeMinutes')->setData($sortie->getDuree()->i);
        }
        //Checker l'etat du form SSI qqn est connecté
        if ($this->getUser()){
            $user = $this->getUser();

            //Check si le mec modifie bien sa propre sortie
            if ($this->getUser() !== $sortie->getOrganisateur()){
                $form->addError(new FormError("Vous ne pouvez modifier que les sorties que vous avez crées"));
            }
            //Check si le mec modifie bien une sortie pas publiée
            if ($sortie->isEstPublie()){
                $form->addError(new FormError("Vous ne pouvez modifier que les sorties non publiées"));
            }

            if ($form->isSubmitted() &&
                $form->isValid()  &&
                $this->container->get('security.authorization_checker')->isGranted('ROLE_USER') &&
                $this->getUser() === $sortie->getOrganisateur() &&
                !$sortie->isEstPublie())
            {


                $sortie->setDuree(DateInterval::createFromDateString($form->get('dureeMinutes')->getData()." min"));
                $sortie ->setOrganisateur($this->getUser());
                $sortie ->setEstPublie($_POST['action'] == 'publier');

                $entityManager -> persist($sortie);
                $entityManager ->flush();

                $this->addFlash("success","La sortie : " . $sortie->getNom() . " à bien été publiée !");

                return $this->redirectToRoute('app_main');

            }}
            //Msg d'erreur si pas connecté
            else{
                $form->addError(new FormError("Vous devez être connecté pour modifier cette sortie"));
            }


        return $this->render('sortie/update.html.twig', [
            'controller_name' => 'SortieController',
            'form' => $form,
            'sortieID' => $sortie->getId()
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/submit-justification/{id}', name: 'submit_justification', methods: ['POST'])]
    public function submitJustification(Sortie $sortie, Request $request, SessionInterface $session, AnnulerSortieService $annulerSortieService, ParticipantRepository $participantRepository): Response
    {
        $form = $this->createForm(JustificationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $justification = $form->get('justification')->getData();

            // Traitement de l'annulation
            if ($this->getUser()) {
                $annulerSortieService->annulerSortie($sortie->getId(),$justification,$this->getUser());
            }

            return $this->json([
                'success' => true,
                'message' => 'Justification enregistrée avec succès'
            ]);
        }

        return $this->json([
            'success' => false,
            'errors' => $form->getErrors(true)
        ], Response::HTTP_BAD_REQUEST);
    }

    public function modalAction(): Response
    {
        $form = $this->createForm(JustificationFormType::class);

        return $this->render('sortie/annulerModal.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
