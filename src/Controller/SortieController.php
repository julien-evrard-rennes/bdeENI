<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\RechercheSortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SortieController extends AbstractController
{
    #[Route('', name: 'accueil')]
    public function accueil(): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(RechercheSortieType::class, $sortie);

        return $this->render('sortie/accueil.html.twig',[
            'sortieForm' => $sortieForm
        ]);
    }
}
