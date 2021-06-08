<?php

namespace App\Form;

use App\Entity\PPBase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PPKeywordsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(

                'keywords',

                TextType::class,

                [
                    'label' => 'Mots-Clés (séparer avec des virgules , ) :',

                    'attr' => [

                        'placeholder'    => 'Mots-Clés (séparer avec des virgules , )',
                    ],

                    'required'   => false,
                ]
            );;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PPBase::class,
        ]);
    }
}
