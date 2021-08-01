<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UpdatePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'oldPassword',
                PasswordType::class,
                [
                    'label' => 'Ancien mot de passe',

                    'attr' => [
                            
                        'placeholder'    => 'Votre ancien mot de passe',
                    ],

                    'required'   => true,
                    
                ]
            )
            
            ->add(
                'newPassword',
                RepeatedType::class,

                [
                    'type' => PasswordType::class,
                    'required'   => true,
                    'options' => ['attr' => ['class' => 'password-field']],
                    'invalid_message' => 'Les deux champs doivent correspondre.',

                    'first_options'  => [
                        
                        'label' => 'Nouveau mot de passe',

                        'attr' => [
                                
                            'placeholder'    => 'Écrire ici',
                        ],
                        
                        'constraints' => [

                            new \Symfony\Component\Validator\Constraints\NotBlank( 
                                [
                                    'message' => 'Ce champ ne peut être vide'
                                ]
                            ),
                            new \Symfony\Component\Validator\Constraints\Length(
                                [
                                    "min" => 4,
                                    "max" => 250,
                                    "minMessage" => "Votre Mot de Passe doit faire minimum {{ limit }} caractères",
                                    "maxMessage" => "Votre Mot de Passe doit faire maximum {{ limit }} caractères",
                                ]
                            ),

                        ]
                        
                    ],

                    'second_options' => [
                        
                        'label' => 'Nouveau mot de passe',

                        'attr' => [
                                
                            'placeholder'    => 'Écrire ici',
                        ],
                    
                    ],

                    
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
