<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MainController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function accueil(): Response
    {
        return $this->render("sortie/accueil.html.twig");
    }
    #[Route('/Profil', name: 'profil')]
    public function profil(): Response
    {
        return $this->render("profil/profil.html.twig");
    }
    #[Route('/ProfilUpdate', name: 'profilUpdate')]
    public function profilUpdate(): Response
    {
        return $this->render("profil/profilUpdate.html.twig");
    }



}