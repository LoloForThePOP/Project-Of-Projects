<?php

namespace App\Form;

use App\Form\VideoSlideType;
use Symfony\Component\Form\AbstractType;
use App\Form\ImageSlideWithoutVichHelperType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
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

                        'placeholder'    => 'Écrire ici',
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

            ->add('answer', TextareaType::class, 
                [
                    'label' => 'Votre réponse',
                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici la réponse',
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

                ]
            )

            ->add(
                'needTitle', 
                TextType::class,
                [

                    'label' => '✒️ Donnez un titre à votre besoin',
                    'required'   => false,
                    'attr' => [

                        'placeholder'    => "Exemple : Un local à Paris",
                    ],
                ]
            )
            ->add(
                'needDescription', 
                TextareaType::class,
                [

                    'label' => '📄 Décrivez votre besoin',
                    'required'   => false,
                    'attr' => [

                        'placeholder'    => "Exemple : Nous recherchons un local pour pouvoir développer notre projet. L'idéal serait 30 m² au minimum, si possible à une distance raisonnable du centre ville.",

                        'rows' => '7',
                    ],
                ]
            )

            ->add(

                'logoFile',
                FileType::class,

                [
                    'label' => 'Cliquer pour sélectionner votre logo',

                    'attr' => [

                        'placeholder'    => '',
                    ],

                    'required'   => false,

                    'constraints' => [
                        new Image([
                            'maxSize' => '5000k',
                            'maxSizeMessage' => "Le poids maximal accepté pour chaque logo est de {{ limit }} {{ suffix }}",
                            'mimeTypes' => ["image/png", "image/jpeg", "image/jpg", "image/webp"],
                            'mimeTypesMessage' => "Pour ajouter un logo, le format de fichier ({{ type }}) n'est pas encore pris en compte. Les formats acceptés sont : {{ types }}",
                           
                        ])
                    ]

                ]



            )

            
            ->add('imageSlide', ImageSlideWithoutVichHelperType::class)

            ->add('selectedNeedType', HiddenType::class)

            ->add('helperItemType', HiddenType::class)
            ->add('finalRenderingLabel', HiddenType::class)


            ->add('currentPosition', HiddenType::class)
            ->add('nextPosition', HiddenType::class)
            ->add('repeatedInstance', HiddenType::class, 
            
                [
                    'data' =>'false'
                ]
                    
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
