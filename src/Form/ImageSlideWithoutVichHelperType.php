<?php

namespace App\Form;

use App\Entity\Slide;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ImageSlideWithoutVichHelperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'file',
                FileType::class,
                
                [
                    'label' => 'Cliquer pour sélectionner une image',

                    'attr' => [

                        'placeholder'    => '',
                    ],

                    'required'   => false,

                ]

            )
            ->add(
                'caption',
                TextType::class,
                [
                    'label' => "Vous pouvez ajouter une légende / titre / commentaire (facultatif) à cette image",

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
