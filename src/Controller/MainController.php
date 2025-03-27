<?php

namespace App\Controller;

use App\Entity\Site;
use App\Repository\SortieRepository;
use App\Services\Archiver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(
        SortieRepository $sortieRepository,
        Archiver $archiver,

    ): Response
    {
        $sorties = $sortieRepository->findBy(['etat' => 1]);
        $archiver->archiver($sorties);

        return $this->render('main/index.html.twig',[
            'sorties' => $sorties,
        ]);
    }

}


