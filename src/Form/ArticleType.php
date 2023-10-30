<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title', 
                TextType::class,
                [

                    'label' => "Quel est le titre de l'article ?",
                    'required'   => true,
                    'attr' => [

                        'placeholder'    => "",
                    ],
                ]
            )
            ->add(
                'content', 
                TextareaType::class,
                [

                    'label' => "Contenu de l'article",
                    'required'   => false,
                    'sanitize_html' => true,
                    'attr' => [

                        'class' => "tinymce",
                        'placeholder' => "",
                    ],

                    
                ]
            )
            ->add(
                'thumbnailFile',
                VichImageType::class,

                [
                    'label' => "Vignette pour l'article",

                    'attr' => [

                        'placeholder'    => '',
                    ],

                    'required'   => false,

                    'allow_delete' => false,
                    'download_label' => false,
                    'download_uri' => false,
                    'image_uri' => false,
                    'asset_helper' => true,
                ]
            
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
