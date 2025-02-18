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
     * Affiche les articles en fonction du type (Homme, Femme, Enfant, Mixte, Accessoires)
     * Le paramètre "type" est optionnel et par défaut vaut "Homme"
     */
    #[Route('/articles/{type?}', name: 'article_by_type', defaults: ['type' => 'Homme'], requirements: ['type' => 'Homme|Femme|Enfant|Mixte|Accessoires'])]
    public function listByType(string $type, ManagerRegistry $doctrine): Response
    {
        $articles = $doctrine->getRepository(Article::class)->findBy(
            ['type' => $type],
            ['datePublication' => 'DESC']
        );

        return $this->render('article/list_by_type.html.twig', [
            'articles' => $articles,
            'type' => $type,
        ]);
    }
}