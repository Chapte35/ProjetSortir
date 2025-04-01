<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/villes', name: 'app_villes')]
final class VillesController extends AbstractController
{
    #[Route('/', name: 'app_villes-list')]
    public function index(VilleRepository $repository): Response
    {
        $villes = $repository->findAll();

        return $this->render('villes/index.html.twig', [
           "villes" => $villes,
        ]);
    }


    #[Route('/creer', name: 'app_villes_creer')]
    public function creer(Request $request, EntityManagerInterface $entityManager): Response
    {

        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->redirectToRoute('app_villesapp_villes-list');
        }

        return $this->render('villes/creer.html.twig', [
            "form" => $form->createView(),

        ]);
    }
}
