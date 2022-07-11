<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MiscDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name', TextType::class, 
                [
                    'label' => 'Titre de la donnée',

                    'attr' => [
                        
                        'placeholder'    => 'Ex : Budget du Projet',
                    ],

                    'required'   => true,

                    'constraints' => array(
                        new \Symfony\Component\Validator\Constraints\NotBlank( 
                            [
                                'message' => 'Ce champ ne peut être vide'
                            ]
                        ),
                        new \Symfony\Component\Validator\Constraints\Length(
                            [
                                "min" => 2,
                                "max" => 250,
                                "minMessage" => "Le nom de la donnée doit contenir au minimum {{ limit }} caractères",
                                "maxMessage" => "Le nom de la donnée doit contenir au plus {{ limit }} caractères",
                            ]
                        ),
                    )
                ]
            )

            ->add('value', TextareaType::class, 
                [
                    'label' => 'Contenu de la donnée',

                    'attr' => [
                        
                        'placeholder'    => 'Ex : 2300 €',
                    ],

                    'required'   => false,
                    
                    'constraints' => array(
                        new \Symfony\Component\Validator\Constraints\NotBlank( 
                            [
                                'message' => 'Ce champ ne peut être vide'
                            ]
                        ),
                        new \Symfony\Component\Validator\Constraints\Length(
                            [
                                "min" => 1,
                                "max" => 250,
                                "minMessage" => "Le contenu de la donnée doit contenir au minimum {{ limit }} caractères",
                                "maxMessage" => "Le contenu de la donnée doit contenir au plus {{ limit }} caractères",
                            ]
                        ),
                    )
                ]
            )

            ->add('source', TextType::class, 
                [
                    'label' => 'Ajouter une source',

                    'attr' => [
                        
                        'placeholder'    => 'Exemple : wikipedia - consulté en 2022',
                    ],

                    'required'   => false,
                    
                    'constraints' => array(
                        new \Symfony\Component\Validator\Constraints\Length(
                            [
                                "min" => 1,
                                "max" => 250,
                                "minMessage" => "La source doit contenir au minimum {{ limit }} caractères",
                                "maxMessage" => "La source doit contenir au plus {{ limit }} caractères",
                            ]
                        ),
                    )
                ]
            )

            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
