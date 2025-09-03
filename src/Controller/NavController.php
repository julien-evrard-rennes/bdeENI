<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NavController extends AbstractController
{
    #[Route('/nav', name: 'nav')]
    public function nav(): Response
    {
        $chiffre = rand(0, 5);

        return $this->render('views/menu/nav.html.twig', [
            'chiffre' => $chiffre,
        ]);
    }
}
