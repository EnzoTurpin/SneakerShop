<?php
// src/Controller/LandingController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;



class LandingController extends AbstractController
{
    // Route pour la page de destination
    #[Route('/', name: 'landing')]
    public function index(): Response
    {
        return $this->render('landing/index.html.twig');
    }

    #[Route('/mentions_legales', name: 'mentions_legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('pages/mentions_legales.html.twig');
    }

    #[Route('/aide-support', name: 'aide_support')]
    public function support(): Response
    {
        return $this->render('pages/aide_support.html.twig');
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('pages/contact.html.twig');
    }
    

    #[Route('/contact/submit', name: 'contact_submit', methods: ['POST'])]
    public function submit(Request $request): Response
    {
        // Récupérer les données du formulaire
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $subject = $request->request->get('subject');
        $message = $request->request->get('message');

        // Ici, tu peux traiter les données (ex: envoyer un email, stocker en BDD, etc.)

        // Ajout d'un message flash pour informer l'utilisateur
        $this->addFlash('success', 'Votre message a bien été envoyé !');

        // Rediriger vers la page de contact
        return $this->redirectToRoute('pages/contact');
    }
}