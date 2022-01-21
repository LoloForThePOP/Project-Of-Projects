<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BuyerInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add(
                
                'email', 

                EmailType::class,  
                
                [
                    'label' => 'Votre adresse e-mail',

                    'attr' => [

                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,
                ]
            
            )

            ->add(

                'message',
                TextareaType::class,
                [

                    'label' => 'Décrivez votre demande sans engagement',

                    'required'   => true,

                    'attr' => [

                        'placeholder'    => "Écrire ici",
                        'rows' => '7',
                    ],
                ]
                
            )
            ->add(
                
                'phone', 

                TelType::class,  
                
                [
                    'label' => 'Un téléphone (recommandé pour faciliter les échanges)',

                    'attr' => [

                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,
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
