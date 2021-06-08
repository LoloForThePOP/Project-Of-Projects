<?php

namespace App\Form;

use App\Entity\PPBase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CreatePresentationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(

                'goal',

                TextType::class,

                [
                    'label' => 'Objectif du Projet',

                    'attr' => [

                        'placeholder'    => 'Écrire ici l\'objectif',
                    ],

                    'required'   => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PPBase::class,
        ]);
    }
}
