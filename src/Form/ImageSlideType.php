<?php

namespace App\Form;

use App\Entity\Slide;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ImageSlideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'file',
                VichImageType::class,
                array(
                    'label'     => 'Choisir une image',
                    'required'     => false,
                    'allow_delete' => false,
                    'download_label' => false,
                    'download_uri' => false,
                    'image_uri' => false,
                    'asset_helper' => true,
                )
            )
            ->add(
                'caption',
                TextType::class,
                [
                    'label' => "Légende / Titre (facultatif) pour cette image",

                    'attr' => [

                        'placeholder'    => "Écrire ici",
                    ],

                    'required'   => false,
                ]
            )
            ->add(
                'licence',
                TextType::class,
                [
                    'label' => "Crédits ou droits d'utilisation de l'image - ©",

                    'attr' => [

                        'placeholder'    => "Ex : Image Wikipedia CC BY-SA 4.0",
                    ],

                    'required'   => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Slide::class,
        ]);
    }
}
