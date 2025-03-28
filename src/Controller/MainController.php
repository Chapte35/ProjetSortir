<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\Filter\SortieFilterType;
use App\Repository\SortieRepository;
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
        SortieRepository $sortieRepository,
        Request $request,
        FilterBuilderUpdater $filterBuilderUpdater,
        EntityManagerInterface $em
    ): Response
    {
        $filterForm = $this->createForm(SortieFilterType::class);

        $filterForm->handleRequest($request);

        $filterBuilder = $em
            ->getRepository(Sortie::class)
            ->createQueryBuilder('sortie');

        $filterBuilderUpdater->addFilterConditions($filterForm, $filterBuilder);


        if ($filterForm->isSubmitted()) {
            $sorties = $sortieRepository->rechercheSorties($filterBuilder,$filterForm->getData(), $this->getUser());
            dd($sorties);
            return $this->render('main/index.html.twig',[
                'sorties' => $sorties,
                'form' => $filterForm
            ]);
        }else{
            $sorties = $sortieRepository->findAll();
        }



        return $this->render('main/index.html.twig',[
            'sorties' => $sorties,
            'form' => $filterForm
        ]);
    }

}