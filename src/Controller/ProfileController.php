<?php
// src/Controller/ProfileController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(): Response
    {
        // Simuler des données d'un utilisateur pour l'exemple
        $user = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ];

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }
}
