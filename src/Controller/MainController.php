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

//        foreach ($sorties as $sortie) {
//            echo "Sortie: " . $sortie->getNom() . "<br>";
//            echo "Participants: <br>";
//            foreach ($sortie->getParticipants() as $participant) {
//                echo "- " . $participant->getNom() . "<br>";
//            }
//        }


        return $this->render('main/index.html.twig',[
            'sorties' => $sorties,
        ]);
    }

}


