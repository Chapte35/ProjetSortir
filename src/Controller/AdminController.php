<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\UploadCsvType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
final class AdminController extends AbstractController
{

    #[Route('/', name: 'app_admin')]
    public function index()
    {
        return $this->render('admin/index.html.twig', []);
    }

    #[Route('/register', name: 'app_admin_register')]
    public function register()
    {
        return $this->redirectToRoute('app_register');
    }


    #[Route('/upload', name: 'admin_upload')]
    public function upload(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UploadCsvType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $csvFile = $form->get('csvFile')->getData();

            if ($csvFile) {
                $filePath = $csvFile->getPathname();

                if (($handle = fopen($filePath, 'r')) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                        $participantId = $data[0];
                        $sortieId = $data[1];

                        $participant = $entityManager->getRepository(Participant::class)->find($participantId);
                        $sortie = $entityManager->getRepository(Sortie::class)->find($sortieId);

                        if ($participant && $sortie) {
                            $participant->addMesInscription($sortie);
                            $entityManager->persist($participant);
                        }
                    }
                    $entityManager->flush();
                    fclose($handle);
                }

                $this->addFlash('success', 'Participants on etait inscrit!');
                return $this->redirectToRoute('app_main');
            }
        }

        return $this->render('admin/upload.html.twig', [

            'form' => $form->createView(),
        ]);
    }


    #[Route('/userlist', name: 'app_admin_userlist')]
    public function userlist(ParticipantRepository $participantRepository): Response
    {
        $users = $participantRepository->findAll();
        $users = array_filter($users, function ($user) {
            return !in_array('ROLE_ADMIN', $user->getRoles());
        });
        return $this->render('admin/userlist.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/desactiver/{id}', name: 'app_admin_desactiver')]
    public function desactiver(ParticipantRepository $participantRepository,EntityManagerInterface $entityManager,int $id): Response
    {

        $participants = $participantRepository->find($id);
        $user = $participants->setActif(false);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_app_admin_userlist');
    }

    #[Route('/supprimer/{id}', name: 'app_admin_supprimer')]
    public function supprimer(ParticipantRepository $participantRepository,EntityManagerInterface $entityManager,int $id): Response
    {

        $participants = $participantRepository->find($id);
        $entityManager->remove($participants);
        $entityManager->flush();

        return $this->redirectToRoute('admin_app_admin_userlist');
    }


}

