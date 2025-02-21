<?php

// src/Controller/LandingController.php

namespace App\Controller;

use App\Repository\ArticleRepository; // Assure-toi que cette ligne est bien présente
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LandingController extends AbstractController
{
    #[Route('/', name: 'landing')]
    public function index(ArticleRepository $articleRepository): Response
    {
        // Récupérer les 5 derniers articles
        $articles = $articleRepository->findBy([], null, 5);


        // Passer les articles à la vue
        return $this->render('landing/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
