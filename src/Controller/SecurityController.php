<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'connex')]
    public function login(AuthenticationUtils $authenticationUtils, ParticipantRepository $participantRepository): Response
    {
        // Récupère l'éventuelle erreur d'authentification

        $error = $authenticationUtils->getLastAuthenticationError();

        // Dernier identifiant saisi (email)
        $lastUsername = $authenticationUtils->getLastUsername();

        //if ($participantRepository->findOneBy(['mail' => $lastUsername])->isActif()) {
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'error_message' => $error ? 'Identifiant ou mot de passe incorrect' : null,
        ]);
        //}
        //else {
        //    $this->addFlash('danger', 'Votre compte est désactivé. Veuillez contacter un administrateur.');
        //    return $this->redirectToRoute('');
        //}
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
