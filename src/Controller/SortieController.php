<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\JustificationFormType;
use App\Form\SortiesType;
use App\Repository\EtatRepository;
use App\Repository\GroupePriveRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Service\AnnulerSortieService;
use DateInterval;
use DateTime;
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
    public function creer(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository, GroupePriveRepository $groupePriveRepository): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortiesType::class, $sortie);

        $publier = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
        $creer = $etatRepository->findOneBy(['libelle' => 'Créée']);



        $form->handleRequest($request);

        if ($this->getUser()){
            $user = $this->getUser();


        if ($form->isSubmitted() && $form->isValid()  && $this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){

            $debut = $sortie->getDateHeureDebut();
            $cloture = $sortie->getDateLimiteInscription();

            if ($form->get('groupe')->getData() && $_POST['action'] == 'publier') {
                $groupeVide = ($form->get('groupe')->getData());
                $groupe = $groupePriveRepository->find($groupeVide->getId());
                foreach ($groupe->getMembres() as $participant) {
                    $sortie->addParticipant($participant);
                }
                if ($request->getSession()->get('is_mobile')) {
                    throw $this->createAccessDeniedException("Création de sortie interdite sur mobile.");
                }
            }

//            $sortie->addParticipant($user);
            $sortie->setDuree(DateInterval::createFromDateString($form->get('dureeMinutes')->getData()." min"));
            $sortie ->setOrganisateur($this->getUser());
            if ($_POST['action'] == 'publier' && $this->getUser()){
                $sortie -> setEtat($publier);
                $sortie->addParticipant($this->getUser());
            }else{
                $sortie -> setEtat($creer);
            }



            $sortie ->setEstPublie($_POST['action'] == 'publier');
            if ($debut<$cloture){
                throw $this->createAccessDeniedException("La date de cloture est incorrect !");
            }

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

    #[Route('/inscrire/{id}', name: 'inscrire')]
    public function inscrire(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository, Sortie $sortie): Response{
        $etat = $sortie->getEtat()->getLibelle();
        $nbInsriptions = count($sortie->getParticipants());
        $nbInsriptionsMax = $sortie->getNbInscriptionsMax();




        if ($etat != 'Ouverte'){
            $this->addFlash("warning","La sortie n'est pas publiée !");
        }

        if ($nbInsriptions >= $nbInsriptionsMax){
            $this->addFlash("warning","Ya pu d'place !");
        }


        if($etat == 'Ouverte'){

            $sortie->addParticipant($this->getUser());

            $entityManager->persist($sortie);
            $entityManager->flush();
        }


        return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
    }


//Gestion des inscriptions
    #[Route('/detail/{id}', name: 'detail')]
    public function detail(int $id,SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);


        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie,
        ]);

    }



    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/modifier/{id}', name: 'modifier')]
    public function update(Sortie $sortie, Request $request, EntityManagerInterface $entityManager, SessionInterface $session, GroupePriveRepository $groupePriveRepository,EtatRepository $etatRepository): Response
    {
        $form = $this->createForm(SortiesType::class, $sortie);
        $publier = $etatRepository->findOneBy(['libelle' => 'Ouverte']);


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
            if ($sortie->getEtat()->getLibelle() != 'Créée'){
                $form->addError(new FormError("Vous ne pouvez modifier que les sorties non publiées"));
            }

            if ($form->isSubmitted() &&
                $form->isValid()  &&
                $this->container->get('security.authorization_checker')->isGranted('ROLE_USER') &&
                $this->getUser() === $sortie->getOrganisateur() &&
                !$sortie->isEstPublie())
            {

                if ($form->get('groupe')->getData() && $_POST['action'] == 'publier') {
                    $groupeVide = ($form->get('groupe')->getData());
                    $groupe = $groupePriveRepository->find($groupeVide->getId());
                    foreach ($groupe->getMembres() as $participant) {
                        $sortie->addParticipant($participant);
                    }
                }

                $sortie->setDuree(DateInterval::createFromDateString($form->get('dureeMinutes')->getData()." min"));
                $sortie ->setOrganisateur($this->getUser());
                if ($this->getUser() && $_POST['action'] == 'publier') {
                    $sortie->setEtat($publier);
                    $sortie->addParticipant($this->getUser());
                }

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
            'sortieID' => $sortie->getId(),
            'sortie' => $sortie
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/submit-justification/{id}', name: 'submit_justification', methods: ['POST'])]
    public function submitJustification(EtatRepository $etatRepository,Sortie $sortie, Request $request, SessionInterface $session, AnnulerSortieService $annulerSortieService, ParticipantRepository $participantRepository): Response
    {

        $form = $this->createForm(JustificationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $justification = $form->get('justification')->getData();


            if ($this->getUser() !== $sortie->getOrganisateur()){
                return $this->json([
                    'success' => false,
                    'errors' => "Seul l'organisateur peut annuler une sortie."
                ], Response::HTTP_BAD_REQUEST);
            }

            // Traitement de l'annulation
            if ($this->getUser()){
//              ASSIGNER UN ETEAT ANULLE ICI
                $annulerSortieService->annulerSortie($sortie->getId(),$justification,$this->getUser());

            }

            if (new DateTime() > $sortie->getDateHeureDebut()) {
                $form->addError(new FormError("C'est trop tard pour annuller la sortie."));
                return $this->json([
                    'success' => false,
                    'errors' => "C'est trop tard pour annuller la sortie."
                ], Response::HTTP_BAD_REQUEST);
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

    #[Route('/desister/{id}', name: 'app_sortie_sedesister')]
    public function seDesister(Sortie $sortie, Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        if($sortie->getEtat()->getLibelle() == 'Cloturée' ){
            $this->addFlash('warning', 'La Sortie a déjà commencé');
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        if($sortie->getEtat()->getLibelle() == 'Ouverte'){
            $sortie->removeParticipant($this->getUser());
            $entityManager->persist($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
    }


    #[Route('/supprimer/{id}', name: 'supprimer')]
    public function supprimer(Sortie $sortie, EntityManagerInterface $entityManager): Response{

        if ($this->getUser() === $sortie->getOrganisateur()){
            $entityManager->remove($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('app_main');
        }


        return $this->redirectToRoute('app_main');

    }

    public function modalAction(): Response
    {
        $form = $this->createForm(JustificationFormType::class);

        return $this->render('sortie/annulerModal.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
