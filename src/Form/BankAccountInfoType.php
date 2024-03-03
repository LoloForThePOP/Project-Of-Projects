<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Bic;
use Symfony\Component\Validator\Constraints\Iban;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class BankAccountInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add(
                'surnameName',
                TextType::class,
                [
                    'label' => 'Nom et prénom du titulaire du compte',

                    'attr' => [
                        
                        'placeholder'    => '',
                    ],

                    'required'   => true,

                    'constraints' => array(
                    
                        new \Symfony\Component\Validator\Constraints\Length(
                            [
                                "min" => 6,
                                "max" => 50,
                                "minMessage" => "Ce champ doit contenir au minimum {{ limit }} caractères",
                                "maxMessage" => "Ce champ doit contenir au maximum {{ limit }} caractères",
                            ]
                        ),
                    )
                ]
            )

            ->add(
                'iban',
                TextType::class,
                [
                    'label' => 'IBAN',

                    'attr' => [
                        
                        'placeholder'    => '',
                    ],

                    'required'   => true,

                    'constraints' => array(
                    
                        new Iban(
                            [
                                "message" => "Veuillez écrire un iban valide",
                            ]
                        ),
                    )
                ]
            )

            ->add(
                'bic',
                TextType::class,
                [
                    'label' => 'BIC',

                    'attr' => [
                        
                        'placeholder'    => '',
                    ],

                    'required'   => true,

                    'constraints' => array(
                    
                        new Bic(
                            [
                                "message" => "Veuillez écrire un BIC valide",
                            ]
                        ),
                    )
                ]
            )

            ->add(
                'acceptTerms',
                CheckboxType::class,
                [
                    'label' => "Accepter les conditions d'utilisation",

                    'attr' => [
                        
                        'placeholder'    => '',
                    ],

                    'required'   => true,

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
