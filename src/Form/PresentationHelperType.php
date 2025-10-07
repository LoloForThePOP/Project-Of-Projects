<?php

namespace App\Form;

use App\Entity\PPBase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Form\ImageSlideWithoutVichHelperType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PresentationHelperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

             ->add(

                'goal',

                TextType::class,

                [
                    'label' => "Quel est l'objectif du Projet ?",

                    'attr' => [

                        'placeholder'    => 'Écrire ici l\'objectif',
                    ],

                    'required'   => true,
                ]
            )

            ->add(
                'title',
                TextType::class,

                [
                    'label' => 'Titre du Projet',

                    'attr' => [

                        'placeholder'    => 'Écrire ici le titre',
                    ],

                    'required'   => false,
                ]

            )

            ->add('textDescription', TextareaType::class, 
                [
                    'label' => 'Votre réponse',
                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                        'rows' => '5',
                        'autofocus' => true,
                    ],
                    'required'   => false,
                    'constraints' => array(
                    
                    )
                ]
            )

            


            ->add(
                'websiteDescription',
                TextType::class,
                [
                    'label' => "Titre de cette adresse ?",
                    'attr' => [

                        'placeholder'    => 'Exemple : Compte Instagram; Site web officiel; etc.',
                    ],
                    'required'   => false,
                    'mapped' => false,
                ]
            )

            ->add(
                'url',
                UrlType::class,
                [

                    'label' => 'Quelle est son adresse ?',
                    'attr' => [

                        'placeholder'    => 'www.exemple.com',
                    ],
                    'required'   => false,
                    'mapped' => false,

                ]
            )
            
            ->add('imageSlide', ImageSlideWithoutVichHelperType::class, [
                'mapped' => false,
            ])

            ->add('helperItemType', HiddenType::class, [
                'mapped' => false,
            ])
           
            ->add('currentPosition', HiddenType::class, [
                'mapped' => false,
            ])

            ->add('nextPosition', HiddenType::class, [
                'mapped' => false,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PPBase::class,
        ]);
    }
}
