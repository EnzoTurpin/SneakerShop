<?php
// src/Controller/HomeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    // Route de la page d'accueil
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('landing/index.html.twig');
    }
}