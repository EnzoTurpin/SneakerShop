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
     * Page pour ajouter un nouvel article
     */
    #[Route('/admin/add-article', name: 'add_article')]
    public function newArticle(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $article->setDatePublication(new \DateTime());

        // Création du formulaire et traitement de la requête
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de l'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                // Générer un nom de fichier sécurisé
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Déplacer l'image dans le dossier uploads (doit exister dans public/)
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'exception si le fichier ne peut être déplacé
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }

                // Mise à jour de l'entité avec le nom du fichier uploadé
                $article->setImageUrl('/uploads/'.$newFilename);
            }

            // Enregistrement de l'article en base de données
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
     * Page pour afficher le détail d'un article
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

    // Méthodes d'affichage des articles par type (le responsive est géré dans les templates Twig)

    #[Route('/articles/homme', name: 'article_homme')]
    public function listHomme(ManagerRegistry $doctrine): Response
    {
        $articles = $doctrine->getRepository(Article::class)->findBy(
            ['type' => 'Homme'],
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
            ['type' => 'Femme'],
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
            ['type' => 'Enfant'],
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
            ['type' => 'Mixte'],
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
            ['type' => 'Accessoires'],
            ['datePublication' => 'DESC']
        );
        return $this->render('article/list_all.html.twig', [
            'articles' => $articles,
            'type' => 'Accessoires',
        ]);
    }

    /**
     * Liste des articles filtrés par catégorie et sous-catégorie.
     * Exemple d'URL : /articles/homme/nike ou /articles/femme/nouveaute
     */
    #[Route('/articles/{category}/{subcategory}', name: 'article_by_category', requirements: [
        'category' => '(?i:Homme|Femme|Enfant|Mixte|Accessoires)',
        'subcategory' => '.+'
    ])]
    public function listByCategory(string $category, string $subcategory, ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Article::class);
        /** @var \Doctrine\ORM\EntityRepository $repository */

        // Traitement pour "nouveaute" ou "nouveauté"
        if (mb_strtolower(trim($subcategory)) === 'nouveaute' || mb_strtolower(trim($subcategory)) === 'nouveauté') {
            $qb = $repository->createQueryBuilder('a')
                ->where('a.type = :category')
                ->andWhere('a.nouveaute = true')
                ->setParameter('category', ucfirst(mb_strtolower($category)))
                ->orderBy('a.datePublication', 'DESC');
            $articles = $qb->getQuery()->getResult();
        } else {
            // Filtrer par marque
            $brand = ucfirst(mb_strtolower(trim($subcategory)));
            $articles = $repository->findBy(
                ['type' => ucfirst(mb_strtolower($category)), 'brand' => $brand],
                ['datePublication' => 'DESC']
            );
        }

        return $this->render('article/list_all.html.twig', [
            'articles'    => $articles,
            'type'        => ucfirst(mb_strtolower($category)),
            'subcategory' => $subcategory,
        ]);
    }
}