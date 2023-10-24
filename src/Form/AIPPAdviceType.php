<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AIPPAdviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add(
                'ppTarget', 
                TextType::class,
                [

                    'label' => 'ppTarget',
                    'required'   => true,
                    'attr' => [

                        'placeholder'    => "Écrire ici.",
                    ],
                ]
            )
            ->add(
                'ppFormat', 
                TextType::class,
                [

                    'label' => 'ppFormat',
                    'required'   => true,
                    'attr' => [

                        'placeholder'    => "Écrire ici.",
                    ],
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
