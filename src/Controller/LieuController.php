<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/lieu', name: 'app_lieu')]
final class LieuController extends AbstractController
{
    #[Route('/cree', name: 'app_lieu')]
    public function list(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $lieu->setLatitude('1');
            $lieu->setLongitude('1');

            $entityManager->persist($lieu);
            $entityManager->flush();

            return $this->redirectToRoute('app_main');

        }


        return $this->render('lieu/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
