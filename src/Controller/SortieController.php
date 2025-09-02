<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SortieController extends AbstractController
{
    #[Route('', name: 'accueil')]
    public function accueil(): Response
    {

        return $this->render('sortie/accueil.html.twig',);
    }
}
