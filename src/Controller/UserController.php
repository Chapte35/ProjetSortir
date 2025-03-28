<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use App\Services\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/Profil', name: 'app_profil')]
    public function Profil(): Response{

        $user = $this->getUser();
        $pseudo = $user->getPseudo();
        $nom = $user->getNom();
        $prenom = $user->getPrenom();
        $email = $user->getEmail();
        $telephone = $user->getTelephone();
        $site = $user->getSite()->getNom();




        return $this->render('user/Profil.html.twig',[
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'site' => $site,
            'pseudo' => $pseudo,
            'imageSrc' => $user->getImage()
        ]);
    }


    #[Route(path: '/Profil/{id}', name: 'app_profil_participant')]
    public function ProfilParticipant(
        int $id,
        ParticipantRepository $participantRepository,
    ): Response{

        $participant = $participantRepository->find($id);
        $pseudo = $participant->getPseudo();
        $nom = $participant->getNom();
        $prenom = $participant->getPrenom();
        $email = $participant->getEmail();
        $telephone = $participant->getTelephone();
        $site = $participant->getSite()->getNom();



        return $this->render('user/Profil.html.twig',[
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'site' => $site,
            'pseudo' => $pseudo,
            'imageSrc' => $participant->getImage()
        ]);
    }

}

