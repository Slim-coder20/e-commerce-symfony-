<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Subcategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('image', FileType::class, [
                'label' => 'Image du produit',
                'help' => 'Formats acceptés : JPG, PNG, GIF, JPEG',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Votre image de produit doit être au format JPG, PNG ou GIF',
                        'maxSizeMessage' => 'La taille de votre image de produit ne doit pas dépasser 1 Mo',
                    ])
                ]
            ])
            ->add('subCategories', EntityType::class, [
                'class' => Subcategory::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
