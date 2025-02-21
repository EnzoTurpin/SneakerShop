<?php

// src/Controller/SalesController.php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SalesController extends AbstractController
{
    #[Route('/sales', name: 'sales')]
    public function index(ArticleRepository $articleRepository): Response
    {
        // Récupérer tous les articles disponibles
        $articles = $articleRepository->findAll();

        return $this->render('sales/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
