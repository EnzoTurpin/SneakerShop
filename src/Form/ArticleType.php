<?php
// src/Form/ArticleType.php
// src/Form/ArticleType.php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'article',
            ])
            ->add('prix', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Prix',
            ])
            ->add('tailles', TextType::class, [
                'required' => false,
                'label' => 'Tailles de chaussure (séparées par des virgules)',
            ])
            // Champ pour uploader l'image (non mappé)
            ->add('imageFile', FileType::class, [
                'label' => 'Image de l\'article (fichier JPG, PNG...)',
                'mapped' => false, // n'est pas lié directement à l'entité Article
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
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
         $resolver->setDefaults([
              'data_class' => Article::class,
         ]);
    }
}