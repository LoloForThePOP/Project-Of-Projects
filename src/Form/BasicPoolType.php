<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BasicPoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            ->add('date', HiddenType::class, [
                'required' => false,
                'data' => date("Y-m-d H:m"),
            ])

            ->add(
                'shouldAdd',
                TextareaType::class,
                [

                    'label' => 'Que devrait-on ajouter sur le site ?',

                    'required'   => false,

                    'attr' => [

                        'placeholder'    => "Écrire ici",
                        'rows' => '2',
                    ],
                ]
                
            )

            ->add(
                'theWorst',
                TextareaType::class,
                [

                    'label' => 'Ce que vous aimez le moins sur le site ?',

                    'required'   => false,

                    'attr' => [

                        'placeholder'    => "Écrire ici",
                        'rows' => '2',
                    ],
                ]
                
            )

            ->add(
                'theBest',
                TextareaType::class,
                [

                    'label' => 'Ce que vous préférez sur le site ?',

                    'required'   => false,

                    'attr' => [

                        'placeholder'    => "Écrire ici",
                        'rows' => '2',
                    ],
                ]
                
            )
            
            ->add(
                
                'rank', 
                HiddenType::class,

                [

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
