<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\Filter\SortieFilterType;
use App\Repository\SortieRepository;

use App\Services\StateHandler;
use Doctrine\ORM\EntityManagerInterface;
use Spiriit\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(
        SortieRepository       $sortieRepository,
        StateHandler           $stateHandler,
        Request                $request,
        FilterBuilderUpdater   $filterBuilderUpdater,
        EntityManagerInterface $em
    ): Response
    {
        $sorties = $sortieRepository->findAll();
        $stateHandler->handleStates($sorties);

        $filterForm = $this->createForm(SortieFilterType::class);

        $filterForm->handleRequest($request);

//        $filterBuilder = $em
//            ->getRepository(Sortie::class)
//            ->createQueryBuilder('sortie');
//
//        $filterBuilderUpdater->addFilterConditions($filterForm, $filterBuilder);

        $sorties = [];

        if ($filterForm->isSubmitted()) {
            $sorties = $sortieRepository->rechercheSorties($filterForm, $this->getUser());
        }else{
            $sorties = $sortieRepository->getSortiesAVenir();
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('main/_sorties_list.html.twig', [
                'sorties' => $sorties
            ]);
        }


        return $this->render('main/index.html.twig',[
            'sorties' => $sorties,
            'form' => $filterForm
        ]);
    }

}


