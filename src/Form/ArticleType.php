<?php
// src/Form/ArticleType.php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('description', TextareaType::class)
            ->add('prix', MoneyType::class, [
                'currency' => 'EUR',
            ])
            ->add('datePublication', DateTimeType::class, [
                'widget' => 'single_text',
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
            // Nouveau champ pour la marque
            ->add('brand', ChoiceType::class, [
                'choices'  => [
                    'Nike'    => 'Nike',
                    'Adidas'  => 'Adidas',
                    'Puma'    => 'Puma',
                    'Reebok'  => 'Reebok',
                ],
                'placeholder' => 'Choisissez une marque (optionnel)',
                'required' => false,
            ])
            // Nouveau champ pour indiquer si c'est une nouveauté
            ->add('nouveaute', CheckboxType::class, [
                'label'    => 'Nouvelle paire ?',
                'required' => false,
            ])
            // Champ pour uploader l'image (non mappé)
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
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}