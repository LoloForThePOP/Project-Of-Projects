<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PresentationHelperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('answer', TextareaType::class, 
                [
                    'label' => 'Votre réponse',
                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici la réponse',
                        'rows' => '5',
                        'autofocus' => true,
                    ],
                    'required'   => false,
                    'constraints' => array(
                    
                    )
                ]
            )
            ->add('helperType', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
