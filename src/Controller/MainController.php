<?php

namespace App\Controller;

use App\Entity\Site;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(
        SortieRepository $sortieRepository,

    ): Response
    {
        $sorties = $sortieRepository->findBy(['estPublie' => 1]);
        return $this->render('main/index.html.twig',[
            'sorties' => $sorties,
        ]);
    }

}


