<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BusinessCardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Un nom, une fonction ?',

                    'attr' => [
                        
                        'placeholder'    => 'Ex : Laurent Dupond ; Responsable Communication',
                    ],

                    'required'   => false,

                    'constraints' => array(
                       
                        new \Symfony\Component\Validator\Constraints\Length(
                            [
                                "max" => 50,
                                "maxMessage" => "Le nom doit contenir au maximum {{ limit }} caractères",
                            ]
                        ),
                    )
                ]
            )

            ->add(
                'email1',
                EmailType::class,

                [
                    'label' => 'Une adresse e-mail ?',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,

                    'constraints' => array(
                        new \Symfony\Component\Validator\Constraints\Email(['message' => 'Veuillez saisir une adresse e-mail valide']),
                    )
                ]
            )

            ->add(
                'tel1', 
                TelType::class,
                [
                    'label' => 'Un téléphone ?',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,

                ]

            )

            ->add(
                'website1',
                UrlType::class,
                [
                    'label' => 'Un site web ou réseau social ?',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,

                    'constraints' => array(

                        new \Symfony\Component\Validator\Constraints\Url(['message' => 'Veuillez utiliser une adresse web valide']),
                    )

                 
                ]
            )

            ->add(
                'website2',
                UrlType::class,
                [
                    'label' => 'Un autre site web ou réseau social ?',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,

                    'constraints' => array(

                        new \Symfony\Component\Validator\Constraints\Url(['message' => 'Veuillez utiliser une adresse web valide']),
                    )

                 
                ]
            )

            ->add(
                'postalMail', 
                TextareaType::class,
                [
                    'label' => 'Une adresse postale ?',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,

                ]
            )

            ->add(
                'remarks',
                TextareaType::class,
                [
                    'label' => 'Ajouter des informations ou des remarques ?',

                    'attr' => [
                        
                        'placeholder'    => "Ex : horaires d'ouverture",
                    ],

                    'required'   => false,

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
