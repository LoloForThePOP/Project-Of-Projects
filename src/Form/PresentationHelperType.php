<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PresentationHelperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                    'label' => 'Titre de l\'adresse',
                    'attr' => [

                        'placeholder'    => 'Exemple : Compte Twitter, Site web officiel, etc.',
                    ],
                    'required'   => false,
                ]
            )

            ->add(
                'url',
                UrlType::class,
                [

                    'label' => 'Adresse du site',
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

                    'label' => 'Titre du besoin',
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

                    'label' => 'Description',
                    'required'   => false,
                    'attr' => [

                        'placeholder'    => "Exemple : Nous recherchons un local pour pouvoir développer notre projet. L'idéal serait 30 m² au minimum, si possible à une distance raisonnable du centre ville.",

                        'rows' => '7',
                    ],
                ]
            )

            ->add('selectedNeedType', HiddenType::class)

            ->add('helperItemType', HiddenType::class)
            ->add('questionAsked', HiddenType::class)
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
