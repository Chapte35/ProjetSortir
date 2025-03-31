<?php

namespace App\Controller;

use App\Entity\GroupePrive;
use App\Form\GroupePriveType;
use App\Repository\GroupePriveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/groupe', name: 'groupe_')]
final class GroupePriveController extends AbstractController
{

    #[Route('/acceuil', name: 'accueil')]
    public function accueil(): Response
    {
        return $this->render('groupe_prive/index.html.twig', [
            'controller_name' => 'GroupePriveController',
        ]);
    }
    #[Route('/liste', name: 'liste')]
    public function liste(GroupePriveRepository $groupeRepo): Response
    {
        return $this->render('groupe_prive/liste.html.twig', [
            'controller_name' => 'GroupePriveController',
            'groupes' => $groupeRepo->findBy(['proprio' => $this->getUser()])
        ]);
    }

    #[Route('/detail/{id}', name: 'detail')]
    public function detail(GroupePrive $groupe): Response
    {
        return $this->render('groupe_prive/detail.html.twig', [
            'controller_name' => 'GroupePriveController',
            'groupe' => $groupe
        ]);
    }

    #[Route('/creer', name: 'creer')]
    public function creer(EntityManagerInterface $entityManager, Request $request): Response
    {


        $gp = new GroupePrive();
        $form = $this->createForm( GroupePriveType::class, $gp);
        $form->handleRequest($request);

        if (!$this->getUser()){
            $form->addError(new FormError("Vous devez être connecté pour créer un groupe privé"));
        }

        if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {

            $gp->setProprio($this->getUser());

            $entityManager->persist($gp);
            $entityManager->flush();

            $this->addFlash("success" , "Le groupe a bien été créé");
            return $this->redirectToRoute('groupe_liste');

        }

        return $this->render('groupe_prive/creer.html.twig', [
            'controller_name' => 'GroupePriveController',
            'form' => $form
        ]);
    }

    #[Route('/modifier/{id}', name: 'modifier')]
    public function modifier(EntityManagerInterface $entityManager, Request $request, GroupePrive $gp): Response
    {
        $form = $this->createForm( GroupePriveType::class, $gp);
        $form->handleRequest($request);

        if (!$this->getUser()){
            $form->addError(new FormError("Vous devez être connecté pour modifier un groupe privé"));
        }

        if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {

            $gp->setProprio($this->getUser());

            $entityManager->persist($gp);
            $entityManager->flush();

            $this->addFlash("success" , "Le groupe a bien été créé");
            return $this->redirectToRoute('groupe_liste');

        }

        return $this->render('groupe_prive/creer.html.twig', [
            'controller_name' => 'GroupePriveController',
            'form' => $form
        ]);
    }

    #[Route('/supprimer/{id}', name: 'supprimer')]
    public function supprimer(EntityManagerInterface $entityManager,GroupePrive $gp): Response
    {

        if ($this->getUser() === $gp->getProprio()){
            $entityManager->remove($gp);
            $entityManager->flush();

            $this->addFlash("success" , "Le groupe a bien été supprimé");
            return $this->redirectToRoute('groupe_liste');
        }

        return $this->render('groupe_prive/supprimer.html.twig', [
            'controller_name' => 'GroupePriveController',
        ]);
    }

}
