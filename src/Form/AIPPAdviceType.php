<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AIPPAdviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add(
                'ppTarget', 
                TextareaType::class,
                [

                    'label' => 'ppTarget',
                    'required'   => true,
                    'attr' => [

                        'placeholder'    => "Écrire ici. Apportez des précisions pour obtenir une meilleure présentation.",
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
