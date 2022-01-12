<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class BuyerInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add(
                
                'email', 

                EmailType::class,  
                
                [
                    'label' => 'Votre addresse e-mail',

                    'attr' => [

                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,
                ]
            
            )

            ->add(
                
                'phone', 

                TelType::class,  
                
                [
                    'label' => 'Téléphone (recommandé)',

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
