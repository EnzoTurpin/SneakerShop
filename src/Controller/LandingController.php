<?php
// src/Controller/LandingController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LandingController extends AbstractController
{
    // Route pour la page de destination
    #[Route('/', name: 'landing')]
    public function index(): Response
    {
        return $this->render('landing/index.html.twig');
    }
}