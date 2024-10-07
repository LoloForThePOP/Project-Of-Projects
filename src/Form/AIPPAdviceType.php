<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;



/**
 * Form to ask user for its:
 * 
 *  - project presentation targeted audience (ex: investors; general public; etc) 
 *  - project presentation format (ex: powerpoint presentation; youtube presentation)
 * 
 * So that we give her/him project presentation advices.
 *
 */

class AIPPAdviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            //project presentation targeted audience

            ->add(
                'ppTarget', 
                TextType::class,
                [

                    'label' => 'ppTarget',
                    'required'   => true,
                    'attr' => [

                        'placeholder'    => "Écrire votre cas ici",
                    ],
                ]
            )

            //Project presentation format

            ->add(
                'ppFormat', 
                TextType::class,
                [

                    'label' => 'ppFormat',
                    'required'   => true,
                    'attr' => [

                        'placeholder'    => "Écrire votre cas ici",
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
