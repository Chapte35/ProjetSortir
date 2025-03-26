<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Services\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserCrudController extends AbstractController
{

    #[Route('/user/crud', name: 'app_user_crud')]
    public function index(): Response
    {
        return $this->render('user_crud/index.html.twig', [
            'controller_name' => 'UserCrudController',
        ]);
    }

    #[Route('/user/crud/new', name: 'app_user_crud_new')]
    public function new(): Response
    {
        return $this->render('user_crud/new.html.twig', [
            'controller_name' => 'UserCrudController',
        ]);
    }

    #[Route('/user/crud/show', name: 'app_user_crud_show')]
    public function show(): Response
    {
        return $this->render('user_crud/show.html.twig', [
            'controller_name' => 'UserCrudController',
        ]);
    }

//modif du profil
    #[Route('/user/crud/edit', name: 'app_user_crud_edit', methods: ['GET', 'POST'])]
    public function edit(
        ParticipantRepository  $participantRepository,
        Request                $request,
        EntityManagerInterface $entityManager,
        Uploader               $uploader,
        //int $id
    ): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        //Création du formulaire
        $participantForm = $this->createForm(ParticipantType::class, $user);
        //Récupérer les données du formulaire et les mettres dans le formulaire modification
        $participantForm->handleRequest($request);
        //Verifier si formulaire soumis et valide
        if ($participantForm->isSubmitted() && $participantForm->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_profil');
        }
        return $this->render('edit.html.twig', [
            'participantForm' => $participantForm->createView(),
        ]);
    }


//      Pour l'upload de photo
//            $photo = $participantForm->get('image')->getData();
//            $participantForm->setImage();
//            $uploader->save($photo, $participantForm->getName(), $this->getParameter('photo_profil_dir')
//            );
//
//            $entityManager->persist($photo);
//            $entityManager->flush();
//
//            $this->addFlash('success', 'Changement effectuer');
//
//        } return $this->render('user_crud/edit.html.twig', [
//        'controller_name' => 'UserCrudController',
//        $this->render('user_crud/edit.html.twig'),
//        'participantForm' => $participantForm->createView(),
//    ]);
//        }

    #[Route('/user/crud/delete', name: 'app_user_delete', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function delete(
        ParticipantRepository  $participantRepository,
        EntityManagerInterface $entityManager
    ): Response
 {
     $entityManager->remove($participantRepository);
     $entityManager->flush();

        $this->addFlash("success","Utilisateur supprimé !");

        return $this->redirectToRoute('app_main');

    }
}






