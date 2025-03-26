<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortiesType;
use App\Repository\EtatRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

            $this->redirectToRoute('app_main');

        }}else{
            $form->addError(new FormError("Vous devez être connecté pour publier cette sortie"));
        }

        return $this->render('sortie/creer.html.twig', [
            'controller_name' => 'SortieController',
            'form' => $form
        ]);
    }
}
