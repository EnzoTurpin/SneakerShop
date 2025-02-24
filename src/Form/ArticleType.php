<?php
// src/Form/ArticleType.php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Construction du formulaire pour l'entité Article
        // On définit ici les différents champs et leurs options
        $builder
            ->add('nom', TextType::class)
            ->add('description', TextareaType::class)
            ->add('prix', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Prix ',
            ])
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                    'Enfant' => 'Enfant',
                    'Mixte' => 'Mixte',
                    'Accessoires' => 'Accessoires',
                ],
                'placeholder' => 'Choisissez un type',
            ])
            ->add('tailles', TextType::class, [
                'required' => false,
                'label' => 'Tailles (optionnel)',
            ])
            ->add('brand', ChoiceType::class, [
                'choices' => [
                    'Adidas' => 'Adidas',
                    'Nike'   => 'Nike',
                    'Puma'   => 'Puma',
                    'Reebok' => 'Reebok',
                ],
                'placeholder' => 'Sélectionnez une marque',
                'label' => 'Marque',
                'required' => false,
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité',
                'data' => 1,
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Neuf' => 'Neuf',
                    'Occasion' => 'Occasion',
                ],
                'placeholder' => 'Choisissez l\'état',
                'required' => true,
            ])
            ->add('nouveaute', CheckboxType::class, [
                'label'    => 'Nouvelle paire ?',
                'required' => false,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image de l\'article (fichier JPG, PNG...)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG ou PNG)',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // Association du formulaire à l'entité Article
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}