<?php
// src/Controller/ProfileController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(Request $request): Response
    {
        // Simuler des données d'un utilisateur pour l'exemple
        $user = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+33 1 23 45 67 89',
        ];

        // Créer un formulaire pour modifier les informations de l'utilisateur
        $form = $this->createFormBuilder($user)
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('phone', TextType::class, ['label' => 'Téléphone'])
            ->add('save', SubmitType::class, ['label' => 'Modifier'])
            ->getForm();

        // Gérer la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();

            // Ici, vous pouvez mettre à jour les données de l'utilisateur dans la base de données
            // Pour cet exemple, nous allons simplement afficher un message de succès
            $this->addFlash('success', 'Informations mises à jour avec succès !');

            // Rediriger ou afficher un message
            return $this->redirectToRoute('profile');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
