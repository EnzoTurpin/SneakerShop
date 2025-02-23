<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function addToCart(int $id, Request $request, ArticleRepository $articleRepository, SessionInterface $session): JsonResponse
    {
        $cart = $session->get('cart', []);

        $article = $articleRepository->find($id);
        if (!$article) {
            return new JsonResponse(['message' => 'Article non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $taille = $data['taille'] ?? null;

        if (!$taille) {
            return new JsonResponse(['message' => 'Veuillez sélectionner une taille'], 400);
        }

        if (!isset($cart[$id])) {
            $cart[$id] = [];
        }

        if (isset($cart[$id][$taille])) {
            $cart[$id][$taille]++;
        } else {
            $cart[$id][$taille] = 1;
        }

        $session->set('cart', $cart);

        return new JsonResponse([
            'message' => "Article ajouté au panier (Taille: $taille)",
            'cart' => $cart
        ]);
    }

    #[Route('/cart', name: 'cart', methods: ['GET'])]
    public function showCart(SessionInterface $session, ArticleRepository $articleRepository)
    {
        $cart = $session->get('cart', []);
        $articles = [];
        $totalPrice = 0;

        foreach ($cart as $id => $sizes) {
            $article = $articleRepository->find($id);
            if ($article) {
                foreach ($sizes as $taille => $quantite) {
                    $articles[] = [
                        'article' => $article,
                        'taille' => $taille,
                        'quantite' => $quantite
                    ];
                    $totalPrice += $article->getPrix() * $quantite;
                }
            }
        }

        return $this->render('cart/cart.html.twig', [
            'cartItems' => $articles,
            'total_price' => $totalPrice
        ]);
    }

    #[Route('/cart/update/{id}', name: 'cart_update', methods: ['POST'])]
    public function updateQuantity(
        int $id,
        Request $request,
        SessionInterface $session,
        ArticleRepository $articleRepository
    ): JsonResponse {
        $cart = $session->get('cart', []);
        $data = json_decode($request->getContent(), true);
        $nouvelleQuantite = $data['quantite'] ?? null;
        // Optionnellement, vous pouvez transmettre la taille pour cibler une ligne précise
        $taille = $data['taille'] ?? null;

        if (!$nouvelleQuantite || $nouvelleQuantite < 1 || $nouvelleQuantite > 10) {
            return new JsonResponse(['message' => "Quantité invalide"], 400);
        }

        if (!isset($cart[$id])) {
            return new JsonResponse(['message' => "Article non trouvé"], 400);
        }

        // Si une taille est précisée, mise à jour uniquement de cette entrée
        if ($taille !== null) {
            if (!isset($cart[$id][$taille])) {
                return new JsonResponse(['message' => "Taille non trouvée pour cet article"], 400);
            }
            $cart[$id][$taille] = $nouvelleQuantite;
        } else {
            // Sinon, on met à jour la première taille disponible
            $premiereTaille = array_key_first($cart[$id]);
            $taille = $premiereTaille;
            $cart[$id][$premiereTaille] = $nouvelleQuantite;
        }
        $session->set('cart', $cart);

        // Récupérer l'article pour calculer le prix de la ligne
        $article = $articleRepository->find($id);
        if (!$article) {
            return new JsonResponse(['message' => 'Article non trouvé'], 404);
        }

        // Calcul du prix pour la ligne concernée
        $rowQuantite = $cart[$id][$taille];
        $articlePrice = $article->getPrix() * $rowQuantite;

        // Calcul du total du panier
        $totalPrice = 0;
        foreach ($cart as $articleId => $sizes) {
            $a = $articleRepository->find($articleId);
            if ($a) {
                foreach ($sizes as $t => $qty) {
                    $totalPrice += $a->getPrix() * $qty;
                }
            }
        }

        return new JsonResponse([
            'message' => "Quantité mise à jour",
            'article_price' => number_format($articlePrice, 2, ',', ' ') . ' €',
            'total_price'   => number_format($totalPrice, 2, ',', ' ') . ' €',
            'cart' => $cart
        ]);
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function removeFromCart(
        int $id,
        SessionInterface $session,
        ArticleRepository $articleRepository
    ): JsonResponse {
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            $session->set('cart', $cart);

            // Recalcule du total du panier après suppression
            $totalPrice = 0;
            foreach ($cart as $articleId => $sizes) {
                $a = $articleRepository->find($articleId);
                if ($a) {
                    foreach ($sizes as $t => $qty) {
                        $totalPrice += $a->getPrix() * $qty;
                    }
                }
            }

            return new JsonResponse([
                'message' => "Article supprimé du panier",
                'total_price' => number_format($totalPrice, 2, ',', ' ') . ' €'
            ]);
        }

        return new JsonResponse(['message' => "Erreur : Article non trouvé dans le panier"], 400);
    }
}