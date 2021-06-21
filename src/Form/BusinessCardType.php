<?php

namespace App\Form;


use App\Form\ImageUploadType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;
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

                    'required'   => true,
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
                    'label' => 'Ajouter des informations, des remarques ?',

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
