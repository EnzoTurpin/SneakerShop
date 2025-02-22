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

    // Route pour la page "Paires du moment"
    #[Route('/moment', name: 'moment')]
    public function moment(): Response
    {
        return $this->render('pages/moment.html.twig');
    }

    // Route pour la page "Ventes récentes"
    #[Route('/sales', name: 'sales')]
    public function sales(): Response
    {
        return $this->render('pages/sales.html.twig');
    }

    // Route pour la page "Sélection de l'équipe"
    #[Route('/team', name: 'team')]
    public function team(): Response
    {
        return $this->render('pages/team.html.twig');
    }

    // Route pour la page "Mentions Légales"
    #[Route('/legal-notice', name: 'legal_notice')]
    public function legalNotice(): Response
    {
        return $this->render('pages/legal_notice.html.twig');
    }
}
