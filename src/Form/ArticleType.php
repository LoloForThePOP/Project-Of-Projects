<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title', 
                TextType::class,
                [

                    'label' => 'Quel est le titre de ce besoin ?',
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
                    'attr' => [

                        'placeholder' => "",
                    ],
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
