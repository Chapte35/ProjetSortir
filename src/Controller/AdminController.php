<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Form\UploadCsvType;
use App\Repository\EtatRepository;
use App\Repository\GroupePriveRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
final class AdminController extends AbstractController
{

    #[Route('/', name: 'app_admin')]
    public function index()
    {
        return $this->render('admin/index.html.twig', []);
    }





    #[Route('/upload', name: 'admin_upload')]
    public function upload(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $sites = $entityManager->getRepository(Site::class)->findAll();
        $siteChoices = [];
        foreach ($sites as $site) {
            $siteChoices[$site->getNom()] = $site->getId();
        }

        $form = $this->createForm(UploadCsvType::class, null, ['sites' => $siteChoices]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $csvFile = $form->get('csvFile')->getData();
            $password = $form->get('password')->getData();
            $siteId = $form->get('site')->getData();


            $site = $entityManager->getRepository(Site::class)->find($siteId);

            if ($csvFile && $site) {
                $filePath = $csvFile->getPathname();

                if (($handle = fopen($filePath, 'r')) !== FALSE) {

                    fgetcsv($handle, 1000, ',');


                    while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {

                        list($pseudo, $nom, $prenom, $email, $telephone) = $data;


                        $user = new Participant();
                        $user->setPseudo($pseudo);
                        $user->setNom($nom);
                        $user->setPrenom($prenom);
                        $user->setEmail($email);
                        $user->setTelephone($telephone);


                        $user->setPassword($userPasswordHasher->hashPassword($user, $password));


                        $user->setRoles(['ROLE_USER']);
                        $user->setActif(true);
                        $user->setSite($site);


                        $entityManager->persist($user);
                    }


                    $entityManager->flush();
                    fclose($handle);
                }


                $this->addFlash('success', 'Participants have been successfully registered with associated site!');


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
    public function supprimer(
        ParticipantRepository $participantRepository,
        EntityManagerInterface $entityManager,
        int $id,
        GroupePriveRepository $groupePriveRepository
    ): Response {
        $participant = $participantRepository->find($id);

        if (!$participant) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('admin_app_admin_userlist');
        }


        $groupes = $groupePriveRepository->findBy(['proprio' => $participant]);

        foreach ($groupes as $groupe) {
            $groupe->setProprio(null);
        }


        $entityManager->remove($participant);
        $entityManager->flush();


        $this->addFlash('success', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('admin_app_admin_userlist');
    }




    #[Route('/sortie', name: 'app_admin_sortie')]
    public function sortie(sortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findnotAnnulee();


        return $this->render('admin/sorties.html.twig', [
            "sorties" => $sorties,
        ]);
    }

    #[Route('/sortie/annuler/{id}', name: 'app_admin_sortie_annuler')]
    public function annuler(EtatRepository $etatRepository, sortieRepository $sortieRepository,int $id,EntityManagerInterface $entityManager): Response
    {
        $annuler = $etatRepository->findOneBy(['libelle' => 'Annulée']);

        $sortie = $sortieRepository->find($id);
        $sortie ->setEtat($annuler);
        $sortie->setInfosSortie('Sortie annulée par un administrateur');
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('admin_app_admin_sortie');


    }


}

