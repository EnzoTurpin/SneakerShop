<?php
// src/Controller/ArticleController.php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ArticleController extends AbstractController
{
    /**
     * Page pour ajouter un nouvel article (accessible pour les admins)
     */
    #[Route('/admin/add-article', name: 'add_article')]
    public function newArticle(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $article->setDatePublication(new \DateTime());
        // Pour les articles admin, on définit createdByAdmin à true
        $article->setCreatedByAdmin(true);

        // Création du formulaire et traitement de la requête
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de l'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }

                $article->setImageUrl('/uploads/' . $newFilename);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article ajouté avec succès !');
            return $this->redirectToRoute('add_article');
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Page pour afficher le détail d'un article admin
     */
    #[Route('/article/{id}', name: 'article_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, ManagerRegistry $doctrine): Response
    {
        $article = $doctrine->getRepository(Article::class)->find($id);
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé.');
        }
        return $this->render('article/detail.html.twig', [
            'article' => $article,
        ]);
    }

    // MÉTHODES DE LISTING POUR LES ARTICLES ADMIN (createdByAdmin == true)

    #[Route('/articles/homme', name: 'article_homme')]
    public function listHomme(ManagerRegistry $doctrine): Response
    {
        $articles = $doctrine->getRepository(Article::class)->findBy(
            ['type' => 'Homme', 'createdByAdmin' => true],
            ['datePublication' => 'DESC']
        );
        return $this->render('article/list_all.html.twig', [
            'articles' => $articles,
            'type' => 'Homme',
        ]);
    }

    #[Route('/articles/femme', name: 'article_femme')]
    public function listFemme(ManagerRegistry $doctrine): Response
    {
        $articles = $doctrine->getRepository(Article::class)->findBy(
            ['type' => 'Femme', 'createdByAdmin' => true],
            ['datePublication' => 'DESC']
        );
        return $this->render('article/list_all.html.twig', [
            'articles' => $articles,
            'type' => 'Femme',
        ]);
    }

    #[Route('/articles/enfant', name: 'article_enfant')]
    public function listEnfant(ManagerRegistry $doctrine): Response
    {
        $articles = $doctrine->getRepository(Article::class)->findBy(
            ['type' => 'Enfant', 'createdByAdmin' => true],
            ['datePublication' => 'DESC']
        );
        return $this->render('article/list_all.html.twig', [
            'articles' => $articles,
            'type' => 'Enfant',
        ]);
    }

    #[Route('/articles/mixte', name: 'article_mixte')]
    public function listMixte(ManagerRegistry $doctrine): Response
    {
        $articles = $doctrine->getRepository(Article::class)->findBy(
            ['type' => 'Mixte', 'createdByAdmin' => true],
            ['datePublication' => 'DESC']
        );
        return $this->render('article/list_all.html.twig', [
            'articles' => $articles,
            'type' => 'Mixte',
        ]);
    }

    #[Route('/articles/accessoires', name: 'article_accessoires')]
    public function listAccessoires(ManagerRegistry $doctrine): Response
    {
        $articles = $doctrine->getRepository(Article::class)->findBy(
            ['type' => 'Accessoires', 'createdByAdmin' => true],
            ['datePublication' => 'DESC']
        );
        return $this->render('article/list_all.html.twig', [
            'articles' => $articles,
            'type' => 'Accessoires',
        ]);
    }

    /**
     * Liste des articles filtrés par catégorie et sous-catégorie pour les articles admin.
     * Exemple d'URL : /articles/homme/nike ou /articles/femme/nouveaute
     */
    #[Route('/articles/{category}/{subcategory}', name: 'article_by_category', requirements: [
        'category' => '(?i:Homme|Femme|Enfant|Mixte|Accessoires)',
        'subcategory' => '.+'
    ])]
    public function listByCategory(string $category, string $subcategory, ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Article::class);
        /** @var \App\Repository\ArticleRepository $repository */
        if (mb_strtolower(trim($subcategory)) === 'nouveaute' || mb_strtolower(trim($subcategory)) === 'nouveauté') {
            $qb = $repository->createQueryBuilder('a')
                ->where('a.type = :category')
                ->andWhere('a.nouveaute = true')
                ->andWhere('a.createdByAdmin = true')
                ->setParameter('category', ucfirst(mb_strtolower($category)))
                ->orderBy('a.datePublication', 'DESC');
            $articles = $qb->getQuery()->getResult();
        } else {
            $brand = ucfirst(mb_strtolower(trim($subcategory)));
            $articles = $repository->findBy(
                ['type' => ucfirst(mb_strtolower($category)), 'brand' => $brand, 'createdByAdmin' => true],
                ['datePublication' => 'DESC']
            );
        }

        return $this->render('article/list_all.html.twig', [
            'articles'    => $articles,
            'type'        => ucfirst(mb_strtolower($category)),
            'subcategory' => $subcategory,
        ]);
    }

    // MÉTHODES POUR LES VENTES ENTRE PARTICULIERS (annonces utilisateurs)

    /**
     * Liste des ventes entre particuliers.
     * Ici, on sélectionne les articles dont createdByAdmin est false (annonces utilisateurs).
     */
    #[Route('/vente/articles', name: 'vente_articles')]
    public function listVenteArticles(ManagerRegistry $doctrine): Response
    {
        $articles = $doctrine->getRepository(Article::class)->findBy(
            ['createdByAdmin' => false],
            ['datePublication' => 'DESC']
        );

        return $this->render('article/vente_list.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * Page pour permettre aux utilisateurs lambda d'ajouter leurs propres annonces (ventes entre particuliers).
     */
    #[Route('/vente/articles/utilisateur/add', name: 'add_vente_article')]
    public function newVenteArticle(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        $article = new Article();
        $article->setDatePublication(new \DateTime());
        $article->setAuteur($user); // Marquer que c'est une annonce utilisateur
        // createdByAdmin reste false par défaut
        
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        // Bloc de debug pour vérifier le type MIME en cas d'erreur de validation
        if ($form->isSubmitted() && !$form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                dd($imageFile->getMimeType());
            }
            dd($form->getErrors(true));
        }
        
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
                $article->setImageUrl('/uploads/' . $newFilename);
            }
            
            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            
            $this->addFlash('success', 'Annonce ajoutée avec succès !');
            return $this->redirectToRoute('add_vente_article');
        }
        
        return $this->render('article/new_vente.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Fiche détail d'une annonce (vente entre particuliers).
     */
    #[Route('/vente/article/{id}', name: 'vente_article_detail', requirements: ['id' => '\d+'])]
    public function venteArticleDetail(int $id, ManagerRegistry $doctrine): Response
    {
        $article = $doctrine->getRepository(Article::class)->find($id);
        if (!$article || $article->isCreatedByAdmin()) {
            throw $this->createNotFoundException('Annonce non trouvée.');
        }
        return $this->render('article/vente_detail.html.twig', [
            'article' => $article,
        ]);
    }
}