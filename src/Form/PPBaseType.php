<?php

namespace App\Form;

use App\Entity\PPBase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PPBaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add(
                'goal',
                TextareaType::class,
                [
                    'label' => 'Objectif du Projet',

                    'attr' => [

                        'placeholder'    => 'Écrire ici l\'objectif',
                    ],

                    'required'   => true,
                ]
            )

            ->add(
                'title',
                TextType::class,

                [
                    'label' => 'Titre du Projet',

                    'attr' => [

                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,
                ]

            )

            ->add(
                'keywords',
                TextType::class,

                [
                    'label' => 'Mots-Clés (séparer avec des virgules , ) :',

                    'attr' => [

                        'placeholder'    => 'Mots-Clés',
                    ],

                    'required'   => false,
                ]
            )

            ->add(

                'logoFile',
                VichImageType::class,

                [
                    'label' => 'Logo du Projet',

                    'attr' => [

                        'placeholder'    => '',
                    ],

                    'required'   => false,

                    'allow_delete' => true,
                    'download_label' => false,
                    'download_uri' => false,
                    'image_uri' => false,
                    'asset_helper' => true,
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
