<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('uniqueName',
                TextType::class,
                [
                    'label' => 'Category Unique Name (one word)',
                    'attr' => [

                        'placeholder'    => 'Example : arts',
                    ],
                    'required'   => true,
                ])
            ->add(
                'descriptionEn', 
                TextareaType::class,
                [

                    'label' => 'English description',
                    'required'   => true,
                    'attr' => [

                        'placeholder'    => "Type here",
                    ],
                ]
            )
            ->add(
                'descriptionFr', 
                TextareaType::class,
                [

                    'label' => 'Description en Français (Utiliser des Majuscules pour les Noms)',
                    'required'   => false,
                    'attr' => [

                        'placeholder'    => "Écrire ici",
                    ],
                ]
            )
            ->add(
                'imageFile',
                VichImageType::class, 

                array(

                    'label' => 'Choose a SVG ICON with SAME NAME as category unique name above',

                    'required' => false,
                    'allow_delete' => true,
                    'download_label' => false,
                    'download_uri' => false,
                    'image_uri' => false,
                    'asset_helper' => true,

                )

            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
