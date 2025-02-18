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

class ArticleController extends AbstractController
{
    #[Route('/admin/add-article', name: 'add_article')]
    public function newArticle(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
         $article = new Article();
         $article->setDatePublication(new \DateTime());

         $form = $this->createForm(ArticleType::class, $article);
         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
              // Récupérer le fichier uploadé
              $imageFile = $form->get('imageFile')->getData();
              if ($imageFile) {
                  // Générer un nom de fichier sécurisé
                  $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                  $safeFilename = $slugger->slug($originalFilename);
                  $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                  // Déplacer le fichier dans le dossier uploads (vous devez créer ce dossier dans public/)
                  try {
                      $imageFile->move(
                          $this->getParameter('uploads_directory'),
                          $newFilename
                      );
                  } catch (FileException $e) {
                      // gérer l'exception si le fichier ne peut pas être déplacé
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
}