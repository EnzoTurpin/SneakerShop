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

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le fichier uploadé depuis le champ imageFile (non mappé)
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                // Générer un nom de fichier sécurisé
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Déplacer le fichier dans le dossier uploads (doit exister dans public/)
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'exception si le fichier ne peut être déplacé
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }

                // Mettre à jour l'entité avec le nouveau nom de fichier (ou chemin relatif)
                $article->setImageUrl('/uploads/'.$newFilename);
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

    /**
     * Affiche les articles pour "Homme"
     */
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

    #[Route('/articles/{category}/{subcategory}', name: 'article_by_category', requirements: [
        'category' => '(?i:Homme|Femme|Enfant|Mixte|Accessoires)',
        'subcategory' => '.+'
    ])]
    public function listByCategory(string $category, string $subcategory, ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Article::class);
        /** @var \Doctrine\ORM\EntityRepository $repository */

        // Si la sous-catégorie indique "nouveaute" ou "nouveauté" (insensible à la casse)
        if (mb_strtolower(trim($subcategory)) === 'nouveaute' || mb_strtolower(trim($subcategory)) === 'nouveauté') {
            $qb = $repository->createQueryBuilder('a')
                ->where('a.type = :category')
                ->andWhere('a.nouveaute = true')
                ->setParameter('category', ucfirst(mb_strtolower($category)))
                ->orderBy('a.datePublication', 'DESC');
            $articles = $qb->getQuery()->getResult();
        } else {
            // Sinon, filtrer par marque : transformer la sous-catégorie pour que la première lettre soit en majuscule
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