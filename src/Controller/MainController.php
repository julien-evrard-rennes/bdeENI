<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MainController extends AbstractController
{
    public function home(): Response
    {
        return $this->render("base.html.twig");
    }


}